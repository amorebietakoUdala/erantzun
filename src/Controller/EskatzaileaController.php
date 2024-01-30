<?php

/**
 * Description of EskatzaileaController.
 *
 * @author ibilbao
 */

namespace App\Controller;

use App\Entity\Eskatzailea;
use App\Form\EskatzaileaFormType;
use App\Repository\EskakizunaRepository;
use App\Repository\EskatzaileaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/{_locale}/admin/eskatzailea')]
class EskatzaileaController extends AbstractController
{

    public function __construct (
        private readonly EntityManagerInterface $em, 
        private readonly EskatzaileaRepository  $repo,
        private readonly EskakizunaRepository $eskakizunaRepo,) 
    {
    }

    #[Route(path: '/new', name: 'admin_eskatzailea_new')]
    public function new(Request $request)
    {
        $form = $this->createForm(EskatzaileaFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Eskatzailea $eskatzailea */
            $eskatzailea = $form->getData();
            $this->em->persist($eskatzailea);
            $this->em->flush();

            $this->addFlash('success', 'messages.eskatzailea_gordea');

            return $this->redirectToRoute('admin_eskatzailea_list');
        }

        return $this->render('admin/eskatzailea/new.html.twig', [
            'eskatzaileaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'admin_eskatzailea_edit')]
    public function edit(Request $request, Eskatzailea $eskatzailea)
    {
        $form = $this->createForm(EskatzaileaFormType::class, $eskatzailea);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $eskatzailea = $form->getData();
            $this->em->persist($eskatzailea);
            $this->em->flush();
            $this->addFlash('success', 'messages.eskatzailea_gordea');
            return $this->redirectToRoute('admin_eskatzailea_list');
        }

        return $this->render('admin/eskatzailea/edit.html.twig', [
            'eskatzaileaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'admin_eskatzailea_delete')]
    public function delete(Request $request, Eskatzailea $eskatzailea)
    {
        $params = $request->query->all();
        if (!$eskatzailea) {
            $this->addFlash('error', 'messages.eskatzailea_ez_da_existitzen');
            return $this->redirectToRoute('admin_eskatzailea_list', $params);
        }
        $eskakizunak = $this->eskakizunaRepo->findOneBy([
            'eskatzailea' => $eskatzailea,
        ]);
        if ($eskakizunak) {
            $this->addFlash('error', 'messages.ezin_eskatzailea_borratu_eskakizunak_dituelako');
            return $this->redirectToRoute('admin_eskatzailea_list', $params);
        }

        $this->em->remove($eskatzailea);
        $this->em->flush();
        $this->addFlash('success', 'messages.eskatzailea_ezabatua');
        return $this->redirectToRoute('admin_eskatzailea_list', $params);
    }

    #[Route(path: '/{id}', name: 'admin_eskatzailea_show')]
    public function show(Eskatzailea $eskatzailea, LoggerInterface $logger)
    {
        $logger->debug('Showing: ' . $eskatzailea->getId());
        $form = $this->createForm(EskatzaileaFormType::class, $eskatzailea);
        return $this->render('admin/eskatzailea/show.html.twig', [
            'eskatzaileaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/', name: 'admin_eskatzailea_list', options: ['expose' => true])]
    public function list()
    {
        $eskatzaileak = $this->repo->findAll();
        return $this->render('admin/eskatzailea/list.html.twig', [
            'eskatzaileak' => $eskatzaileak,
        ]);
    }
}
