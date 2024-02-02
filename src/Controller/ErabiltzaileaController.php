<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AMREU\UserBundle\Doctrine\UserManager;
use App\Form\PasswordResetRequestFormType;
use App\Form\PasswordResetFormType;
use App\Repository\ErantzunaRepository;
use App\Repository\EskakizunaRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Description of ErabiltzaileakController.
 *
 * @author ibilbao
 */

class ErabiltzaileaController extends AbstractController
{

    public function __construct(
        private readonly TranslatorInterface $translator, 
        private readonly MailerInterface $mailer, 
        private readonly UserManager $userManager, 
        private readonly EntityManagerInterface $em, 
        private readonly UserRepository $userRepo, 
        private readonly EskakizunaRepository $eskakizunaRepo, 
        private readonly ErantzunaRepository $erantzunaRepo
    )
    {
    }

    #[IsGranted('ROLE_ERANTZUN')]
    #[Route(path: '/{_locale}/profile', name: 'user_profile_action')]
    public function profile(Request $request, LoggerInterface $logger)
    {
        /** @var User $user */
        $user = $this->getUser();
        $logger->info('Showing: ' . $user->getUserIdentifier());
        $userForm = $this->createForm(UserFormType::class, $user, [
            'profile' => true,
            'password_change' => true
        ]);

        $previousPassword = $user->getPassword();
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            /** @var User $user */
            $user = $userForm->getData();
            if ('nopassword' === $user->getPassword()) {
                $user->setPassword($previousPassword);
                $this->em->persist($user);
                $this->em->flush();
            } else {
                // This updates and persist the new password, no need to persist it again.
                $this->userManager->updatePassword($user, $user->getPassword());
            }
            $this->addFlash('success', 'messages.erabiltzailea_gordea');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('/admin/erabiltzailea/edit.html.twig', [
            'erabiltzaileaForm' => $userForm->createView(),
            'profile' => true,
            'password_change' => true
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/{_locale}/admin/erabiltzaileak/new', name: 'admin_erabiltzailea_new')]
    public function new(Request $request)
    {
        $form = $this->createForm(UserFormType::class, null, [
            'password_change' => true
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $aurkitutako_erabiltzailea = $this->userRepo->findOneBy([
                'username' => $user->getUsername(),
            ]);

            if ($aurkitutako_erabiltzailea) {
                $this->addFlash('error', 'messages.erabiltzailea_hori_sortuta_dago_jadanik');

                return $this->render('admin/erabiltzailea/new.html.twig', [
                    'erabiltzaileaForm' => $form->createView(),
                    'profile' => false,
                    'password_change' => true,
                ]);
            }

            $aurkitutako_erabiltzailea = $this->userRepo->findOneBy([
                'email' => $user->getEmail(),
            ]);
            if ($aurkitutako_erabiltzailea) {
                $this->addFlash('error', 'messages.erabiltzailea_hori_sortuta_dago_jadanik');

                return $this->render('admin/erabiltzailea/new.html.twig', [
                    'erabiltzaileaForm' => $form->createView(),
                    'profile' => false,
                    'password_change' => true,
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
            'password_change' => true,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/{_locale}/admin/erabiltzaileak/{id}/edit', name: 'admin_erabiltzailea_edit')]
    public function edit(Request $request, User $user)
    {
        $form = $this->createForm(UserFormType::class, $user, [
            'password_change' => true
        ]);
        $previousUsername = $user->getUsername();
        $previousPassword = $user->getPassword();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            if ($previousUsername !== $user->getUsername()) {
                $existingUser = $this->userRepo->findOneBy(['username' => $user->getUsername()]);
                if ( null !== $existingUser ) {
                    $this->addFlash('error','messages.existingUserOnUsernameChange');
                    return $this->render('admin/erabiltzailea/edit.html.twig', [
                        'erabiltzaileaForm' => $form->createView(),
                        'profile' => false,
                        'password_change' => true,
                    ]);                    
                }
            }
            if ('nopassword' === $user->getPassword()) {
                $user->setPassword($previousPassword);
                $this->em->persist($user);
                $this->em->flush();
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
            'password_change' => true,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/{_locale}/admin/erabiltzaileak/{id}/delete', name: 'admin_erabiltzailea_delete')]
    public function delete(User $user)
    {
        if (!$user) {
            $this->addFlash('error', 'messages.erabiltzailea_ez_da_existitzen');

            return $this->redirectToRoute('admin_erabiltzailea_list');
        }

        $eskakizunak = $this->eskakizunaRepo->findOneBy([
            'norkInformatua' => $user,
        ]);

        if ($eskakizunak) {
            $this->addFlash('error', 'messages.ezin_erabiltzailea_borratu_eskakizunak_dituelako');

            return $this->redirectToRoute('admin_erabiltzailea_list');
        } else {
            $eskakizunakErreklamatu = $this->eskakizunaRepo->findOneBy([
                'norkErreklamatua' => $user,
            ]);
            if ($eskakizunakErreklamatu) {
                $this->addFlash('error', 'messages.ezin_erabiltzailea_borratu_eskakizunak_erreklamatu_dituelako');

                return $this->redirectToRoute('admin_erabiltzailea_list');
            }
            $erantzun = $this->erantzunaRepo->findOneBy([
                'erantzulea' => $user,
            ]);
            if ($erantzun) {
                $this->addFlash('error', 'messages.ezin_erabiltzailea_borratu_erantzunak_sartu_dituelako');

                return $this->redirectToRoute('admin_erabiltzailea_list');
            }
        }

        $this->em->remove($user);
        $this->em->flush();

        $this->addFlash('success', 'messages.erabiltzailea_ezabatua');

        return $this->redirectToRoute('admin_erabiltzailea_list');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/{_locale}/admin/erabiltzaileak/{id}', name: 'admin_erabiltzailea_show')]
    public function show(User $erabiltzailea, LoggerInterface $logger)
    {
        $logger->debug('Showing: ' . $erabiltzailea->getId());
        $form = $this->createForm(UserFormType::class, $erabiltzailea);

        return $this->render('admin/erabiltzailea/show.html.twig', [
            'erabiltzaileaForm' => $form->createView(),
            'profile' => false,
            'password_change' => false,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route(path: '/{_locale}/admin/erabiltzaileak/', name: 'admin_erabiltzailea_list', options: ['expose' => true])]
    public function list()
    {
        $erabiltzaileak = $this->userRepo->findAll();

        return $this->render('admin/erabiltzailea/list.html.twig', [
            'erabiltzaileak' => $erabiltzaileak,
        ]);
    }

    #[Route(path: '/{_locale}/request_reset', name: 'user_request_reset_action')]
    public function resetRequest(Request $request)
    {
        $form = $this->createForm(PasswordResetRequestFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            /** @var User $user */
            $user = $this->userRepo->findOneBy(['email' => $data['email']]);
            $token = $this->generateToken();
            $user->setConfirmationToken($token);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->sendResetPasswordMessage($user, $token);

            $this->em->persist($user);
            $this->em->flush();
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

    #[Route(path: '/{_locale}/reset/{token}', name: 'user_reset_password_action', methods: ['GET', 'POST'])]
    public function resetPassword(Request $request, string $token)
    {
        /** @var User $user  */
        $user = $this->userRepo->findOneBy(['confirmationToken' => $token]);

        if (null === $user) {
            $this->addFlash('error', 'messages.erabiltzailea_ez_da_existitzen');
            return $this->redirectToRoute('app_home');
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
