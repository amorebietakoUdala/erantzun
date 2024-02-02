<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[IsGranted('ROLE_ERANTZUN')]
    #[Route(path: '/', name: 'app_home')]
    public function home(Request $request, LoggerInterface $logger)
    {
        /** @var User $user */
        $user = $this->getUser();
        $locale = $request->getLocale();
        if (null !== $locale) {
            $request->getSession()->set('_locale', $locale);
        } else if (null !== $request->getSession()->get('_locale')) {
            $request->setLocale($request->getSession()->get('_locale'));
        } else {
            $request->setLocale($request->getDefaultLocale());
        }
        if ($this->isGranted('ROLE_INFORMATZAILEA')) {
            return $this->redirectToRoute('admin_eskakizuna_new', [
                '_locale' => $request->getLocale(),
            ]);
        } elseif ($this->isGranted('ROLE_ARDURADUNA')) {
            return $this->redirectToRoute('admin_eskakizuna_list', [
            '_locale' => $request->getLocale(),
            ]);
        } elseif ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_eskakizuna_list', [
            '_locale' => $request->getLocale(),
            ]);
        } elseif ($this->isGranted('ROLE_KANPOKO_TEKNIKARIA')) {
            $logger->debug('Kanpoko Teknikaria erabiltzailea: '.$user->getUsername());
        }
        return $this->redirectToRoute('admin_eskakizuna_list', [
            '_locale' => $request->getLocale(),
        ]);
    }
}
