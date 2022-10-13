<?php

namespace App\Controller;

use App\Entity\Erabiltzailea;
use App\Form\ErabiltzaileaFormType;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @IsGranted("ROLE_USER")
     */
    public function homeAction(Request $request, LoggerInterface $logger)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $authorization_checker = $this->get('security.authorization_checker');
        $locale = $request->attributes->get('_locale');
        if (null !== $locale) {
            $request->getSession()->set('_locale', $locale);
        } else if (null !== $request->getSession()->get('_locale')) {
            $request->setLocale($request->getSession()->get('_locale'));
        } else {
            $request->setLocale($request->getDefaultLocale());
        }
        if ($authorization_checker->isGranted('ROLE_INFORMATZAILEA')) {
            return $this->redirectToRoute('admin_eskakizuna_new', [
        '_locale' => $request->getLocale(),
        ]);
        } elseif ($authorization_checker->isGranted('ROLE_ARDURADUNA')) {
            return $this->redirectToRoute('admin_eskakizuna_list', [
        '_locale' => $request->getLocale(),
        ]);
        } elseif ($authorization_checker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_eskakizuna_list', [
        '_locale' => $request->getLocale(),
        ]);
        } elseif ($authorization_checker->isGranted('ROLE_KANPOKO_TEKNIKARIA')) {
            $logger->debug('Kanpoko Teknikaria erabiltzailea: '.$user->getUsername());
        }
        return $this->redirectToRoute('admin_eskakizuna_list', [
            '_locale' => $request->getLocale(),
        ]);
    }
}
