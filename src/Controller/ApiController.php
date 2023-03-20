<?php

namespace App\Controller;

use App\Controller\Web\Admin\EskatzaileaFormType;
use App\Controller\Web\User\ErantzunaFormType;
use App\Entity\Erantzuna;
use App\Entity\Eskakizuna;
use App\Entity\Eskatzailea;
use App\Entity\Egoera;
use App\Repository\ErantzunaRepository;
use App\Repository\EskakizunaRepository;
use App\Repository\EskatzaileaRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/api")
 * @isGranted("ROLE_ERANTZUN")
 */
class ApiController extends AbstractController
{

    private $serializer;
    private EntityManagerInterface $em;
    private EskatzaileaRepository $eskatzaileaRepo;
    // private EskakizunaRepository $eskakizunaRepo;
    // private UserRepository $userRepo;
    private ErantzunaRepository $erantzunaRepo;

    public function __construct(
        SerializerInterface $serializer, 
        EntityManagerInterface $em,
        EskatzaileaRepository $eskatzaileaRepo,
        // EskakizunaRepository $eskakizunaRepo,
        // UserRepository $userRepo,
        ErantzunaRepository $erantzunaRepo)
    {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->eskatzaileaRepo = $eskatzaileaRepo;
        // $this->eskakizunaRepo = $eskakizunaRepo;
        // $this->userRepo = $userRepo;
        $this->erantzunaRepo = $erantzunaRepo;
    }

    /**
     * @Route("/eskatzailea", name="api_eskatzailea_list", options={"expose" = true}, methods={"GET"} )
     */
    public function listAction(Request $request)
    {
        $criteria = $request->query->all();
        if ($criteria) {
            $eskatzaileak = $this->eskatzaileaRepo->findAllLike($criteria);
        } else {
            $eskatzaileak = $this->eskatzaileaRepo->findAll();
        }
        $response = $this->createApiResponse(['eskatzaileak' => $eskatzaileak], 200);

        return $response;
    }

    /**
     * @Route("/eskatzailea", methods={"POST"})
     */
    public function newAction(Request $request)
    {
        $eskatzailea = new Eskatzailea();
        $form = $this->createForm(EskatzaileaFormType::class, $eskatzailea);
        $this->processForm($request, $form);

        $this->em->persist($eskatzailea);
        $this->em->flush();

        $response = $this->createApiResponse($eskatzailea, 201);
        $eskatzaileaUrl = $this->generateUrl('api_eskatzailea_show', [
            'id' => $eskatzailea->getId()
        ]);
        $response->headers->set('Location', $eskatzaileaUrl);

        return $response;
    }

    /**
     * @Route("/eskatzailea/{id}", name="api_eskatzailea_show", methods={"GET"})
     */
    public function showAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ERANTZUN');
        $eskatzailea = $this->eskatzaileaRepo->findOneBy([
            'id' => $id,
        ]);

        if (!$eskatzailea) {
            throw $this->createNotFoundException('Ez da eskatzailerik aurkitu.');
        }

        $response = $this->createApiResponse($eskatzailea, 200);

        return $response;
    }

    /**
     * @Route("/eskakizuna/{id}/erantzuna/new", name="api_erantzuna_new", methods={"GET"}, options={"expose" = true})
     */
    // public function newErantzunaAction(Request $request, $id)
    // {
    //     //	$this->denyAccessUnlessGranted('ROLE_ERANTZUN');
    //     $user = $this->getUser();
    // //     $user = $this->userRepo->findOneBy([
    // //     'username' => 'admin',
    // // ]);
    //     $erantzuna = new Erantzuna();
    //     $form = $this->createForm(ErantzunaFormType::class, $erantzuna);
    //     $this->processForm($request, $form);

    //     $eskakizuna = $this->eskakizunaRepo->findOneBy([
    //         'id' => $id,
    //     ]);

    //     if (null == $eskakizuna) {
    //         throw $this->createNotFoundException('Ez da eskakizuna aurkitu beraz ezin da erantzuna gehitu.');
    //     }

    //     if (null != $eskakizuna) {
    //         $erantzuna->setEskakizuna($eskakizuna);
    //         $erantzuna->setNoiz(new DateTime());
    //         $erantzuna->setErantzulea($user);
    //         $this->em->persist($erantzuna);
    //         $this->em->flush();

    //         $response = $this->createApiResponse($erantzuna, 201);
    //         $erantzunaUrl = $this->generateUrl('api_erantzuna_show',[
    //             'id' => $erantzuna->getId()
    //         ]);
    //         $response->headers->set('Location', $erantzunaUrl);
    //         return $response;
    //     }
    // }

    /**
     * @Route("/erantzuna/{id}", name="api_erantzuna_show", methods={"GET"}, options={"expose" = true})
     */
    public function showErantzunaAction($id)
    {
        $erantzuna = $this->erantzunaRepo->findOneBy([
            'id' => $id,
        ]);
        if (!$erantzuna) {
            throw $this->createNotFoundException('Ez da erantzunik aurkitu.');
        }
        $response = $this->createApiResponse($erantzuna, 200);

        return $response;
    }

    /**
     * @Route("/eskakizuna", name="api_eskakizuna_list", methods={"GET"}, options={"expose" = true} )
     */
    // public function listEskakizunaAction(Request $request)
    // {
    //     /** @var User $user */
    //     $user = $this->getUser();
    //     $bilatzaileaForm->handleRequest($request);

    //     if ($bilatzaileaForm->isSubmitted() && $bilatzaileaForm->isValid()) {
    //         $criteria = $bilatzaileaForm->getData();
    //         $criteria['role'] = null;
    //         if ($this->security->isGranted('ROLE_KANPOKO_TEKNIKARIA')) {
    //             $criteria['enpresa'] = $user->getEnpresa();
    //         }
    //         if (array_key_exists('noiztik', $criteria)) {
    //             $from = $criteria['noiztik'];
    //             $criteria['noiztik'] = null;
    //         }
    //         if (array_key_exists('nora', $criteria)) {
    //             $to = $criteria['nora'];
    //             $criteria['nora'] = null;
    //         }
    //         $criteria_without_blanks = $this->_remove_blank_filters($criteria);
    //         if ($criteria_without_blanks || null != $from || null != $to) {
    //             $eskakizunak = $this->getDoctrine()
    //         ->getRepository(Eskakizuna::class)
    //         ->findAllFromTo($criteria_without_blanks, $from, $to);
    //         } else {
    //             $eskakizunak = $this->getDoctrine()
    //         ->getRepository(Eskakizuna::class)
    //         ->findAllOpen();
    //         }
    //         $response = $this->createApiResponse(['eskakizunak' => $eskakizunak], 200);

    //         return $response;
    //     }

    //     if ($authorization_checker->isGranted('ROLE_KANPOKO_TEKNIKARIA')) {
    //         $eskakizunak = $this->em->getRepository(Eskakizuna::class)->findBy([
    //         'enpresa' => $user->getEnpresa(),
    //         ]
    //     );
    //     } else {
    //         $eskakizunak = $this->getDoctrine()
    //         ->getRepository(Eskakizuna::class)
    //         ->findAllOpen();
    //     }

    //     $response = $this->createApiResponse(['eskakizunak' => $eskakizunak], 200);

    //     return $response;
    // }

    /**
     * @Route("/batchclose", name="api_eskakizuna_batchclose", methods={"POST"})
     */
    public function closeAction(Request $request)
    {
        $ids = \json_decode($request->get('ids'));

        foreach ($ids as $id) {
            $eskakizuna = $this->em->getRepository(Eskakizuna::class)->find($id);
            $eskakizuna->setItxieraData(new \DateTime());
            $egoera = $this->em->getRepository(Egoera::class)->find(Egoera::EGOERA_ITXIA);
            $eskakizuna->setEgoera($egoera);
            $this->em->persist($eskakizuna);
        }
        $this->em->flush();

        return $this->createApiResponse(
            ['Deskripzioa' => 'Eskakizun guztiak ondo itxi dira'], 200);
    }

    private function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);
        $clearMissing = 'PATCH' != $request->getMethod();
        $form->submit($data, $clearMissing);
    }

    protected function createApiResponse($data, $statusCode = 200)
    {
        $json = $this->serialize($data);

        return new Response($json, $statusCode, array(
            'Content-Type' => 'application/json',
        ));
    }
    
    protected function serialize($data, $format = 'json')
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        $context->enableMaxDepthChecks();

        return $this->serializer->serialize($data, $format, $context);
    }    
}
