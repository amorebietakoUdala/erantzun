<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use App\Entity\Argazkia;
use App\Entity\Egoera;
use App\Entity\Enpresa;
use App\Entity\User;
use App\Entity\Erantzuna;
use App\Entity\Eskakizuna;
use App\Entity\Eskatzailea;
use App\Entity\Georeferentziazioa;
use App\Entity\Zerbitzua;
use App\Form\EskakizunaBilatzaileaFormType;
use App\Form\EskakizunaFormType;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Imagick;
use Psr\Log\LoggerInterface;
use Swift_Message;
use \Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of EskakizunaController.
 *
 * @author ibilbao
 */

/**
 * @isGranted("ROLE_USER");
 * @Route("/{_locale}/eskakizuna")
 */
class EskakizunaController extends AbstractController
{
    private $eskatzailea;
    private $eskakizuna;
    private $georeferentziazioa;
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    /**
     * @Route("/new", name="admin_eskakizuna_new", options={"expose" = true})
     */
    public function newAction(Request $request)
    {
        $params = $request->query->all();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $form = $this->createForm(EskakizunaFormType::class, new Eskakizuna(), [
            'editatzen' => false,
            'role' => $user->getRoles(),
            'locale' => $request->getLocale(),
        ]);
        // Only handles data on POST request
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /* @var data \App\Entity\Eskakizuna */
            $this->eskakizuna = $form->getData();
            $this->eskatzailea = $this->eskakizuna->getEskatzailea();
            if (null !== $this->eskatzailea->getId()) {
                $this->eskatzailea = $em->getRepository(Eskatzailea::class)->find($this->eskatzailea->getId());
            } else {
                $this->eskatzailea = new Eskatzailea();
            }

            $this->_parseEskatzailea($form);
            $this->eskakizuna->setEskatzailea($this->eskatzailea);
            $georeferentziazioa = $form->getData()->getGeoreferentziazioa();
            if (null !== $georeferentziazioa->getLongitudea() && null !== $georeferentziazioa->getLatitudea()) {
                $this->eskakizuna->setGeoreferentziazioa($georeferentziazioa);
                $em->persist($georeferentziazioa);
            }
            $zerbitzua = $this->eskakizuna->getZerbitzua();
            $zerbitzua_hautatua = false;
            if (null !== $zerbitzua) {
                $this->eskakizuna->setEnpresa($zerbitzua->getEnpresa());
                $zerbitzua_hautatua = true;
            }
            $this->_argazkia_gorde_multi();
            $this->_eranskinak_gorde_multi();
            if (null != $this->eskakizuna->getZerbitzua()) {
                $egoera = $em->getRepository(Egoera::class)->find(Egoera::EGOERA_BIDALIA);
                $this->eskakizuna->setEgoera($egoera);
                $this->eskakizuna->setNoizBidalia(new DateTime());
            } else {
                $egoera = $em->getRepository(Egoera::class)->find(Egoera::EGOERA_BIDALI_GABE);
                $this->eskakizuna->setEgoera($egoera);
            }

            $this->eskakizuna->setNorkInformatua($user);
            $this->eskakizuna->setNoizInformatua(new DateTime());

            $em->persist($this->eskatzailea);
            $em->persist($this->eskakizuna);
            $em->flush();
            $mezuak_bidali = $this->getParameter('mezuak_bidali');
            if ($mezuak_bidali && $zerbitzua_hautatua) {
                $title = 'Eskakizun Berria. Eskakizun zenbakia:';
                $this->_mezuaBidaliEnpresari($title, $this->eskakizuna, $this->eskakizuna->getEnpresa());
            }

            $this->addFlash('success', 'messages.eskakizuna_gordea');

            return $this->redirectToRoute('admin_eskakizuna_new', $params);
        }

        return $this->render('/eskakizuna/new.html.twig', [
            'eskakizunaForm' => $form->createView(),
            'argazkia' => null,
            // 'erantzunak' => [],
            'erantzun' => false,
            'editatzen' => false,
            'googleMapsApiKey' => $this->getParameter('googleMapsApiKey'),
        ]);
    }

    /**
     * @Route("/atzotik", name="admin_eskakizuna_atzotik", options={"expose" = true})
     */
    public function listAtzotikAction()
    {
        $gaur = new DateTime();

        return $this->redirectToRoute('admin_eskakizuna_list', [
            'nora' => $gaur->format('Y-m-d H:i'),
            'noiztik' => $gaur->modify('-1 day')->format('Y-m-d 00:00'),
        ]);
    }

    /**
     * @Route("/azkenastea", name="admin_eskakizuna_azken_astea", options={"expose" = true})
     */
    public function listAzkenAsteaAction()
    {
        $gaur = new DateTime();

        return $this->redirectToRoute('admin_eskakizuna_list', [
            'nora' => $gaur->format('Y-m-d H:i'),
            'noiztik' => $gaur->modify('-7 day')->format('Y-m-d 00:00'),
        ]);
    }

    /**
     * @Route("/", name="admin_eskakizuna_list", options={"expose" = true})
     */
    public function listAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $authorization_checker = $this->get('security.authorization_checker');
        $session = $request->getSession();
        $this->_setPageSize($request);

        $azkenBilaketa = $this->_getAzkenBilaketa($request);
        $azkenBilaketa['role'] = $user->getRoles();
        $from = array_key_exists('noiztik', $azkenBilaketa) ? $azkenBilaketa['noiztik'] : null;
        $to = array_key_exists('nora', $azkenBilaketa) ? $azkenBilaketa['nora'] : null;
        $criteria = [];
        if ($authorization_checker->isGranted('ROLE_KANPOKO_TEKNIKARIA') || $authorization_checker->isGranted('ROLE_INFORMATZAILEA')) {
            $azkenBilaketa['enpresa'] = $user->getEnpresa();
        }

        $bilatzaileaForm = $this->createForm(EskakizunaBilatzaileaFormType::class, $azkenBilaketa);
        $bilatzaileaForm->handleRequest($request);
        if ($bilatzaileaForm->isSubmitted() && $bilatzaileaForm->isValid()) {
            $criteria = $bilatzaileaForm->getData();
            $session->set('azkenBilaketa', $criteria);
            $from = array_key_exists('noiztik', $criteria) ? $criteria['noiztik'] : null;
            $to = array_key_exists('nora', $criteria) ? $criteria['nora'] : null;
        }
        $criteria = array_merge($azkenBilaketa, $criteria);
        $criteria_without_blanks = $this->_remove_blank_filters($criteria);
        unset($criteria_without_blanks['noiztik']);
        unset($criteria_without_blanks['nora']);
        unset($criteria_without_blanks['role']);
        unset($criteria_without_blanks['locale']);

        if (array_key_exists('egoera', $criteria_without_blanks)) {
            $eskakizunak = $this->getDoctrine()
                ->getRepository(Eskakizuna::class)
                ->findAllFromTo($criteria_without_blanks, $from, $to);
        } else {
            $eskakizunak = $this->getDoctrine()
                ->getRepository(Eskakizuna::class)
                ->findAllOpen($criteria_without_blanks, $from, $to);
            //            dd($eskakizunak);
        }

        return $this->render('/eskakizuna/list.html.twig', [
            'bilatzaileaForm' => $bilatzaileaForm->createView(),
            'eskakizunak' => $eskakizunak,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_eskakizuna_edit")
     */
    public function editAction(Request $request, Eskakizuna $eskakizuna, LoggerInterface $logger)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $form = $this->createForm(EskakizunaFormType::class, $eskakizuna, [
            'editatzen' => true,
            'role' => $user->getRoles(),
            'locale' => $request->getLocale(),
        ]);

        $params = $request->query->all();

        $zerbitzuaAldatuAurretik = $eskakizuna->getZerbitzua();
        $erantzunak = $eskakizuna->getErantzunak();
        $eranskinakAldatuAurretik = new ArrayCollection();

        foreach ($eskakizuna->getEranskinak() as $eranskina) {
            $eranskinakAldatuAurretik->add($eranskina);
        }

        $argazkiakAldatuAurretik = new ArrayCollection();

        foreach ($eskakizuna->getArgazkiak() as $argazkia) {
            $argazkiakAldatuAurretik->add($argazkia);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->eskakizuna = $form->getData();
            $geo = $this->eskakizuna->getGeoreferentziazioa();

            if (null !== $geo && null !== $geo->getId()) {
                $geo = $em->getRepository(Georeferentziazioa::class)->find($eskakizuna->getId());
            } elseif (null !== $geo->getLongitudea() && null !== $geo->getLatitudea()) {
                $this->georeferentziazioa = $geo;
                $this->eskakizuna->setGeoreferentziazioa($this->georeferentziazioa);
                $em->persist($this->georeferentziazioa);
            }

            $logger->debug('Zerbitzua: ' . $eskakizuna->getZerbitzua());
            if (null !== $this->eskakizuna->getZerbitzua()) {
                $zerbitzua = $this->eskakizuna->getZerbitzua();
                $this->eskakizuna->setEnpresa($zerbitzua->getEnpresa());
                if (
                    Egoera::EGOERA_BIDALI_GABE === $this->eskakizuna->getEgoera()->getId()
                    || null !== $zerbitzuaAldatuAurretik && ($zerbitzua->getId() !== $zerbitzuaAldatuAurretik->getId())
                ) {
                    $logger->debug('Egoera: Bidali gabe edo zerbitzua aldatua');
                    $egoera = $em->getRepository(Egoera::class)->find(Egoera::EGOERA_BIDALIA);
                    $this->eskakizuna->setEgoera($egoera);
                    $this->eskakizuna->setNoizBidalia(new DateTime());
                    $title = 'Eskakizuna esleitu egin zaizu. Eskakizun zenbakia:';
                    $mezuak_bidali = $this->getParameter('mezuak_bidali');
                    if ($mezuak_bidali) {
                        $this->_mezuaBidaliEnpresari($title, $this->eskakizuna, $this->eskakizuna->getEnpresa());
                        $logger->debug('Mezua Bidalia');
                    }
                }
            } else {
                $egoera = $em->getRepository(Egoera::class)->find(Egoera::EGOERA_BIDALI_GABE);
                $this->eskakizuna->setEgoera($egoera);
            }

            $form_erantzunak = $this->eskakizuna->getErantzunak();
            $erantzunak_count = $form_erantzunak->count();
            $erantzun_berria = $form_erantzunak['erantzuna'];
            unset($form_erantzunak['erantzuna']);
            if (null !== $erantzun_berria) {
                $erantzuna = new Erantzuna();
                $erantzuna->setErantzulea($user);
                $erantzuna->setEskakizuna($this->eskakizuna);
                $erantzuna->setErantzuna($erantzun_berria);
                $erantzuna->setNoiz(new DateTime());
                $em->persist($erantzuna);
                $erantzunak = $form_erantzunak->getValues();
                array_push($erantzunak, $erantzuna);
            }

            if ($erantzunak_count > 0) {
                $egoera = $em->getRepository(Egoera::class)->find(Egoera::EGOERA_ERANTZUNDA);
                $this->eskakizuna->setEgoera($egoera);
            }

            $form_erantzunak = $this->eskakizuna->getErantzunak();
            $erantzunak_count = $form_erantzunak->count();
            $erantzun_berria = $form_erantzunak['erantzuna'];
            unset($form_erantzunak['erantzuna']);
            if (null !== $erantzun_berria) {
                $erantzuna = new Erantzuna();
                $erantzuna->setErantzulea($user);
                $erantzuna->setEskakizuna($this->eskakizuna);
                $erantzuna->setErantzuna($erantzun_berria);
                $erantzuna->setNoiz(new DateTime());
                $em->persist($erantzuna);
                $erantzunak = $form_erantzunak->getValues();
                array_push($erantzunak, $erantzuna);
            }

            if ($erantzunak_count > 0) {
                $egoera = $em->getRepository(Egoera::class)->find(Egoera::EGOERA_ERANTZUNDA);
                $this->eskakizuna->setEgoera($egoera);
            }

            $this->_argazkia_gorde_multi($argazkiakAldatuAurretik);

            $this->_eranskinak_gorde_multi($eranskinakAldatuAurretik);

            $em->persist($this->eskakizuna);
            $em->flush();

            $this->addFlash('success', 'messages.eskakizuna_gordea');

            return $this->redirectToRoute('admin_eskakizuna_list', $params);
        }

        return $this->render('/eskakizuna/edit.html.twig', [
            'eskakizunaForm' => $form->createView(),
            'erantzunak' => $erantzunak,
            'editatzen' => true,
            'erantzun' => false,
            'googleMapsApiKey' => $this->getParameter('googleMapsApiKey'),

        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_eskakizuna_delete")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $eskakizuna = $em->getRepository(Eskakizuna::class)->findOneBy([
            'id' => $id,
        ]);

        if (!$eskakizuna) {
            $this->addFlash('error', 'messages.eskakizuna_ez_da_existitzen');

            return $this->listAction();
        }

        $params = $request->query->all();
        $em->remove($eskakizuna);
        $em->flush();

        $this->addFlash('success', 'messages.eskakizuna_ezabatua');

        return $this->redirectToRoute('admin_eskakizuna_list', $params);
    }

    /**
     * @Route("/{id}", name="admin_eskakizuna_show")
     */
    public function showAction(Request $request, Eskakizuna $eskakizuna, LoggerInterface $logger)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $logger->debug('Show. Eskakizun zenbakia: ' . $eskakizuna->getId());

        $eskakizunaForm = $this->createForm(EskakizunaFormType::class, $eskakizuna, [
            'editatzen' => false,
            'readonly' => true,
            'role' => $user->getRoles(),
            'locale' => $request->getLocale(),
        ]);

        $erantzunak = $eskakizuna->getErantzunak();
        $eskakizunaForm->handleRequest($request);
        if ($eskakizunaForm->isSubmitted() && $eskakizunaForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->eskakizuna = $eskakizunaForm->getData();
            $form_erantzunak = $this->eskakizuna->getErantzunak();
            $erantzunak_count = $form_erantzunak->count();
            $erantzun_berria = $form_erantzunak['erantzuna'];
            unset($erantzunak['erantzuna']);
            if (null !== $erantzun_berria) {
                $erantzuna = new Erantzuna();
                $erantzuna->setErantzulea($user);
                $erantzuna->setEskakizuna($this->eskakizuna);
                $erantzuna->setErantzuna($erantzun_berria);
                $erantzuna->setNoiz(new DateTime());
                $em->persist($erantzuna);
                $erantzunak = $form_erantzunak->getValues();
                array_push($erantzunak, $erantzuna);
            }
            if (null === $this->eskakizuna->getArgazkia()) {
                $this->eskakizuna->setArgazkia($eskakizuna->getArgazkia());
            }

            // Zerbitzurik ez badauka ez dugu emailik bidaltzen.
            if ($erantzunak_count > 0 && null !== $this->eskakizuna->getZerbitzua()) {
                $egoera = $em->getRepository(Egoera::class)->find(Egoera::EGOERA_ERANTZUNDA);
                $this->eskakizuna->setEgoera($egoera);
                $erantzundakoan_mezua_bidali = $this->getParameter('erantzundakoan_mezua_bidali');
                $mezuak_bidali = $this->getParameter('mezuak_bidali');
                if ($erantzundakoan_mezua_bidali && $mezuak_bidali) {
                    $title = 'Eskakizuna erantzunda. Eskakizun zenbakia: ';
                    $this->_mezuaBidaliArduradunei($title, $this->eskakizuna);
                }
            }
            $em->persist($this->eskakizuna);
            $em->flush();

            $this->addFlash('success', 'messages.erantzuna_gordea');
        }

        return $this->render('/eskakizuna/show.html.twig', [
            'eskakizunaForm' => $eskakizunaForm->createView(),
            'erantzunak' => $erantzunak,
            'editatzen' => false,
            'erantzun' => true,
            'googleMapsApiKey' => $this->getParameter('googleMapsApiKey'),
        ]);
    }

    /**
     * @Route("/{id}/close", name="admin_eskakizuna_close")
     */
    public function closeAction(Request $request, Eskakizuna $eskakizuna)
    {
        $params = $request->query->all();
        if (!$eskakizuna) {
            $this->addFlash('error', 'messages.eskakizuna_ez_da_existitzen');
            $this->redirectToRoute('admin_eskakizuna_list', $params);
        }

        $em = $this->getDoctrine()->getManager();
        $eskakizuna->setItxieraData(new DateTime());
        $egoera = $em->getRepository(Egoera::class)->find(Egoera::EGOERA_ITXIA);
        $eskakizuna->setEgoera($egoera);

        $em->persist($eskakizuna);
        $em->flush();

        $this->addFlash('success', 'messages.eskakizuna_itxia');

        return $this->redirectToRoute('admin_eskakizuna_list', $params);
    }

    /**
     * @Route("/{id}/resend", name="admin_eskakizuna_resend")
     */
    public function resendAction(Request $request, Eskakizuna $eskakizuna)
    {
        $params = $request->query->all();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!$eskakizuna) {
            $this->addFlash('error', 'messages.eskakizuna_ez_da_existitzen');
            $this->redirectToRoute('admin_eskakizuna_list', $params);
        }

        $em = $this->getDoctrine()->getManager();
        $eskakizuna->setNoizErreklamatua(new DateTime());
        $eskakizuna->setNorkErreklamatua($user);
        $title = 'Eskakizuna erreklamatua. Eskakizun zenbakia: ';
        $this->_mezuaBidaliEnpresari($title, $eskakizuna, $eskakizuna->getEnpresa());

        $em->persist($eskakizuna);
        $em->flush();

        $this->addFlash('success', 'messages.eskakizuna_erreklamatua');

        return $this->redirectToRoute('admin_eskakizuna_list', $params);
    }

    private function _parseEskatzailea($form)
    {
        $data = $form->getData();
        $eskatzailea = $data->getEskatzailea();
        $this->eskatzailea->setIzena($eskatzailea->getIzena());
        $this->eskatzailea->setNan($eskatzailea->getNan());
        $this->eskatzailea->setTelefonoa($eskatzailea->getTelefonoa());
        $this->eskatzailea->setFaxa($eskatzailea->getFaxa());
        $this->eskatzailea->setHelbidea($eskatzailea->getHelbidea());
        $this->eskatzailea->setEmaila($eskatzailea->getEmaila());
        $this->eskatzailea->setHerria($eskatzailea->getHerria());
        $this->eskatzailea->setPostaKodea($eskatzailea->getPostaKodea());
    }

    private function _argazkia_kudeatu(Argazkia $argazkia)
    {
        $argazkien_direktorioa = $this->getParameter('images_uploads_directory');
        $argazkien_zabalera = $this->getParameter('images_width');
        $argazkien_thumb_zabalera = $this->getParameter('images_thumb_width');
        $argazkiaren_izena = $argazkia->getImageName();
        if (null !== $argazkia) {
            /* Honek funtzionatzen du baina agian zuzenean txikituta gorde daiteke */
            $image = new Imagick($argazkien_direktorioa . '/' . $argazkiaren_izena);
            $image->thumbnailImage($argazkien_zabalera, 0);
            $image->writeImage($argazkien_direktorioa . '/' . $argazkiaren_izena);
            $imageFile = new File($argazkien_direktorioa . '/' . $argazkiaren_izena);
            $argazkia->setImageFile($imageFile);
            $image->thumbnailImage($argazkien_thumb_zabalera, 0);
            $image->writeImage($argazkien_direktorioa . '/' . 'thumb-' . $argazkiaren_izena);
            $imageThumbnailFile = new File($argazkien_direktorioa . '/' . 'thumb-' . $argazkiaren_izena);
            $argazkia->setImageThumbnailFile($imageThumbnailFile);
            $argazkia->setImageThumbnailSize($imageThumbnailFile->getSize());
        }
    }

    private function _mezuaBidaliArduradunei($title, $eskakizuna)
    {
        $em = $this->getDoctrine()->getManager();
        $jasotzaileak = $em->getRepository(User::class)->findByRole('ROLE_ARDURADUNA');
        $emailak = [];
        foreach ($jasotzaileak as $jasotzailea) {
            $emailak[] = $jasotzailea->getEmail();
        }
        $this->_mezuaBidali($title, $eskakizuna, $emailak);
    }

    private function _mezuaBidaliEnpresari($title, $eskakizuna, Enpresa $enpresa)
    {
        $em = $this->getDoctrine()->getManager();
        $jasotzaileak = $em->getRepository(User::class)->findBy([
            'enpresa' => $enpresa,
            'activated' => true,
        ]);
        $emailak = [];
        foreach ($jasotzaileak as $jasotzailea) {
            $emailak[] = $jasotzailea->getEmail();
        }
        $this->_mezuaBidali($title, $eskakizuna, $emailak);
    }

    private function _mezuaBidali($title, $eskakizuna, $emailak)
    {
        $from = $this->getParameter('mailer_from');
        $message = new Swift_Message($title . ' ' . $eskakizuna->getId());
        $message->setFrom($from);
        $message->setTo($emailak);
        $message->setBody(
            $this->renderView('/eskakizuna/mail.html.twig', [
                'eskakizuna' => $eskakizuna,
            ])
        );
        $message->setContentType('text/html');

        $this->mailer->send($message);
    }

    private function _remove_blank_filters($criteria)
    {
        $new_criteria = [];
        foreach ($criteria as $key => $value) {
            if (!empty($value)) {
                $new_criteria[$key] = $value;
            }
        }

        return $new_criteria;
    }

    private function _argazkia_gorde_multi($argazkiakAldatuAurretik = null)
    {
        $em = $this->getDoctrine()->getManager();
        if (null !== $argazkiakAldatuAurretik) {
            foreach ($argazkiakAldatuAurretik as $aurrekoArgazkia) {
                if (false === $this->eskakizuna->getArgazkiak()->contains($aurrekoArgazkia)) {
                    $this->eskakizuna->removeArgazkiak($aurrekoArgazkia);
                    $em->remove($aurrekoArgazkia);
                }
            }
        }
        $argazkiak = $this->eskakizuna->getArgazkiak();
        if (!$argazkiak->isEmpty()) {
            foreach ($argazkiak as $argaz) {
                if (null !== $argaz->getImageName()) {
                    $argaz->setEskakizuna($this->eskakizuna);
                    $em->persist($argaz);
                    $this->_argazkia_kudeatu($argaz);
                } else {
                    $this->eskakizuna->getArgazkiak()->removeElement($argaz);
                    $em->remove($argaz);
                }
            }
        }
    }

    private function _eranskinak_gorde_multi($eranskinakAldatuAurretik = null)
    {
        $em = $this->getDoctrine()->getManager();
        /* Zaharretatik borratu direnak borratu */
        if (null != $eranskinakAldatuAurretik && !$eranskinakAldatuAurretik->isEmpty()) {
            foreach ($eranskinakAldatuAurretik as $aurrekoEranskina) {
                if (false === $this->eskakizuna->getEranskinak()->contains($aurrekoEranskina)) {
                    $this->eskakizuna->removeEranskinak($aurrekoEranskina);
                    $em->remove($aurrekoEranskina);
                }
            }
        }
        /* Eranskin berriak edo aldatutakoak gorde */
        $eranskinak = $this->eskakizuna->getEranskinak();
        if (!$eranskinak->isEmpty()) {
            foreach ($eranskinak as $erans) {
                $em->persist($erans);
                $erans->setEskakizuna($this->eskakizuna);
            }
        }
    }

    private function _getAzkenBilaketa(Request $request)
    {
        $azkenBilaketa = null;
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        if (null != $session->get('azkenBilaketa')) {
            $azkenBilaketa = $session->get('azkenBilaketa');
            if (array_key_exists('egoera', $azkenBilaketa) && null != $azkenBilaketa['egoera']) {
                $azkenBilaketa['egoera'] = $em->getRepository(Egoera::class)->find($azkenBilaketa['egoera']);
            }
            if (array_key_exists('zerbitzua', $azkenBilaketa) && null != $azkenBilaketa['zerbitzua']) {
                $azkenBilaketa['zerbitzua'] = $em->getRepository(Zerbitzua::class)->find($azkenBilaketa['zerbitzua']);
            }
            if (array_key_exists('enpresa', $azkenBilaketa) && null != $azkenBilaketa['enpresa']) {
                $azkenBilaketa['enpresa'] = $em->getRepository(Enpresa::class)->find($azkenBilaketa['enpresa']);
            }
        } else {
            $azkenBilaketa['locale'] = $request->getLocale();
        }

        return $this->_remove_blank_filters($azkenBilaketa);
    }

    private function _setPageSize(Request $request)
    {
        $session = $request->getSession();
        if (null != $request->query->get('pageSize')) {
            $pageSize = $request->query->get('pageSize');
            $request->query->remove('pageSize');
            $session->set('pageSize', $pageSize);
        } else {
            if (null == $session->get('pageSize')) {
                $session->set('pageSize', 10);
            }
        }
    }
}
