<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\Eskakizuna;
use App\Form\UserFormType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use AMREU\UserBundle\Doctrine\UserManager;
use App\Entity\Erantzuna;
use App\Form\PasswordResetRequestFormType;
use App\Form\PasswordResetFormType;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Description of ErabiltzaileakController.
 *
 * @author ibilbao
 */

class ErabiltzaileaController extends AbstractController
{

    private $translator;
    private MailerInterface $mailer;
    private $userManager;

    public function __construct(TranslatorInterface $translator, MailerInterface $mailer, UserManager $userManager)
    {
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/{_locale}/profile", name="user_profile_action")
     * @IsGranted("ROLE_ERANTZUN")
     */
    public function profleAction(Request $request, LoggerInterface $logger)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $logger->info('Showing: ' . $user->getId());

        $em = $this->getDoctrine()->getManager();
        //        $user = $em->getRepository(User::class)->find($user->getId());
        $userForm = $this->createForm(UserFormType::class, $user, [
            'profile' => true,
            'password_change' => true
        ]);

        $previousPassword = $user->getPassword();
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();
            if ('nopassword' === $user->getPassword()) {
                $user->setPassword($previousPassword);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            } else {
                // This updates and persist the new password, no need to persist it again.
                $this->userManager->updatePassword($user, $user->getPassword());
            }
            $this->addFlash('success', 'messages.erabiltzailea_gordea');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('/admin/erabiltzailea/edit.html.twig', [
            'erabiltzaileaForm' => $userForm->createView(),
            'profile' => true,
            'password_change' => true
        ]);
    }
    /**
     * @Route("/{_locale}/admin/erabiltzaileak/new", name="admin_erabiltzailea_new")
     * @IsGranted("ROLE_ADMIN")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(UserFormType::class, null, [
            'password_change' => true
        ]);

        // Only handles data on POST request
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $aurkitutako_erabiltzailea = $em->getRepository(User::class)->findOneBy([
                'username' => $user->getUsername(),
            ]);

            if ($aurkitutako_erabiltzailea) {
                $this->addFlash('error', 'messages.erabiltzailea_hori_sortuta_dago_jadanik');

                return $this->render('admin/erabiltzailea/new.html.twig', [
                    'erabiltzaileaForm' => $form->createView(),
                    'profile' => false,
                ]);
            }

            $aurkitutako_erabiltzailea = $em->getRepository(User::class)->findOneBy([
                'email' => $user->getEmail(),
            ]);
            if ($aurkitutako_erabiltzailea) {
                $this->addFlash('error', 'messages.erabiltzailea_hori_sortuta_dago_jadanik');

                return $this->render('admin/erabiltzailea/new.html.twig', [
                    'erabiltzaileaForm' => $form->createView(),
                    'profile' => false,
                ]);
            }
            // This persists the user no need to persist again
            $this->userManager->updatePassword($user, $user->getPassword());

            $this->addFlash('success', 'messages.erabiltzailea_gordea');

            return $this->redirectToRoute('admin_erabiltzailea_list');
        }
        return $this->render('admin/erabiltzailea/new.html.twig', [
            'erabiltzaileaForm' => $form->createView(),
            'profile' => false,
            'password_change' => true
        ]);
    }

    /**
     * @Route("/{_locale}/admin/erabiltzaileak/{id}/edit", name="admin_erabiltzailea_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editAction(Request $request, User $user)
    {
        $form = $this->createForm(UserFormType::class, $user, [
            'password_change' => true
        ]);

        $previousPassword = $user->getPassword();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            if ('nopassword' === $user->getPassword()) {
                $user->setPassword($previousPassword);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            } else {
                // This updates and persist the new password, no need to persist it again.
                $this->userManager->updatePassword($user, $user->getPassword());
            }
            $this->addFlash('success', 'messages.erabiltzailea_gordea');

            return $this->redirectToRoute('admin_erabiltzailea_list');
        }
        return $this->render('admin/erabiltzailea/edit.html.twig', [
            'erabiltzaileaForm' => $form->createView(),
            'profile' => false,
        ]);
    }

    /**
     * @Route("/{_locale}/admin/erabiltzaileak/{id}/delete", name="admin_erabiltzailea_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction(Request $request, User $user)
    {
        if (!$user) {
            $this->addFlash('error', 'messages.erabiltzailea_ez_da_existitzen');

            return $this->redirectToRoute('admin_erabiltzailea_list');
        }

        $em = $this->getDoctrine()->getManager();
        $eskakizunak = $em->getRepository(Eskakizuna::class)->findOneBy([
            'norkInformatua' => $user,
        ]);

        if ($eskakizunak) {
            $this->addFlash('error', 'messages.ezin_erabiltzailea_borratu_eskakizunak_dituelako');

            return $this->redirectToRoute('admin_erabiltzailea_list');
        } else {
            $eskakizunakErreklamatu = $em->getRepository(Eskakizuna::class)->findOneBy([
                'norkErreklamatua' => $user,
            ]);
            if ($eskakizunakErreklamatu) {
                $this->addFlash('error', 'messages.ezin_erabiltzailea_borratu_eskakizunak_erreklamatu_dituelako');

                return $this->redirectToRoute('admin_erabiltzailea_list');
            }
            $erantzun = $em->getRepository(Erantzuna::class)->findOneBy([
                'erantzulea' => $user,
            ]);
            if ($erantzun) {
                $this->addFlash('error', 'messages.ezin_erabiltzailea_borratu_erantzunak_sartu_dituelako');

                return $this->redirectToRoute('admin_erabiltzailea_list');
            }
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'messages.erabiltzailea_ezabatua');

        return $this->redirectToRoute('admin_erabiltzailea_list');
    }

    /**
     * @Route("/{_locale}/admin/erabiltzaileak/{id}", name="admin_erabiltzailea_show")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showAction(User $erabiltzailea, LoggerInterface $logger)
    {
        $logger->debug('Showing: ' . $erabiltzailea->getId());
        $form = $this->createForm(UserFormType::class, $erabiltzailea);

        return $this->render('admin/erabiltzailea/show.html.twig', [
            'erabiltzaileaForm' => $form->createView(),
            'profile' => false,
        ]);
    }

    /**
     * @Route("/{_locale}/admin/erabiltzaileak/", name="admin_erabiltzailea_list", options={"expose" = true})
     * @IsGranted("ROLE_ADMIN")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $erabiltzaileak = $em->getRepository('App:User')->findAll();

        return $this->render('admin/erabiltzailea/list.html.twig', [
            'erabiltzaileak' => $erabiltzaileak,
        ]);
    }

    /**
     * @Route("/{_locale}/request_reset", name="user_request_reset_action")
     */
    public function resetRequest(Request $request)
    {
        $form = $this->createForm(PasswordResetRequestFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            /** @var User $user */
            $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            $token = $this->generateToken();
            $user->setConfirmationToken($token);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->sendResetPasswordMessage($user, $token);

            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'messages.sent');

            return $this->redirectToRoute('user_security_login_check');
        }
        return $this->render('admin/erabiltzailea/request_reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function sendResetPasswordMessage(User $user, string $token)
    {
        $from = $this->getParameter('mailer_from');
        $email = (new Email())
            ->from($from)
            ->to($user->getEmail())
            ->subject($this->translator->trans('messages.password_reset_message_title'))
            ->html($this->renderView('/admin/erabiltzailea/reset_password_email.html.twig', [
                    'token' => $token,
                    'user' => $user,
                ]),
                'text/html'
            );
        $this->mailer->send($email);
    }

    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    /**
     * @Route("/{_locale}/reset/{token}", name="user_reset_password_action", methods={"GET","POST"})
     */
    public function resetPassword(Request $request, string $token)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user  */
        $user = $em->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if (null === $user) {
            $this->addFlash('error', 'messages.erabiltzailea_ez_da_existitzen');
            return $this->redirectToRoute('homepage');
        }

        $datediff = date_diff(new \DateTime(),  $user->getPasswordRequestedAt());
        if ($datediff->format('%a') > 1) {
            $this->addFlash('error', 'message.token_expired');
            return $this->redirectToRoute('user_security_login_check');
        }

        $form = $this->createForm(PasswordResetFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->userManager->updatePassword($user, $data['password']);
            $this->addFlash('success', 'message.pasahitza_ondo_aldatu_da');
            return $this->redirectToRoute('user_security_login_check');
        }

        return $this->render('admin/erabiltzailea/reset_password.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
        ]);
    }
}
