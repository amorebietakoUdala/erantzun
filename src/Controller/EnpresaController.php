<?php

/**
 * Description of EnpresaController.
 *
 * @author ibilbao
*/

namespace App\Controller;

use App\Entity\Enpresa;
use App\Form\EnpresaFormType;
use App\Repository\EnpresaRepository;
use App\Repository\EskakizunaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


 #[Route(path: '/{_locale}/admin/enpresa')]
class EnpresaController extends AbstractController
{

    public function __construct (
        private readonly EntityManagerInterface $em,
        private readonly EnpresaRepository $repo,
        private readonly EskakizunaRepository $eskakizunaRepo,
        private readonly LoggerInterface $logger) 
    {
    }

    #[Route(path: '/new', name: 'admin_enpresa_new')]
    public function new(Request $request)
    {
        $form = $this->createForm(EnpresaFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Enpresa $enpresa */
            $enpresa = $form->getData();
            $this->em->persist($enpresa);
            $this->em->flush();
            $this->addFlash('success', 'messages.enpresa_gordea');
            return $this->redirectToRoute('admin_enpresa_list');
        }

        return $this->render('admin/enpresa/new.html.twig', [
            'enpresaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'admin_enpresa_edit')]
    public function edit(Request $request, #[MapEntity(id: 'id')] Enpresa $enpresa)
    {
        $form = $this->createForm(EnpresaFormType::class, $enpresa);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Enpresa $enpresa */
            $enpresa = $form->getData();
            $this->em->persist($enpresa);
            $this->em->flush();
            $this->addFlash('success', 'messages.enpresa_gordea');
            return $this->redirectToRoute('admin_enpresa_list');
        }

        return $this->render('admin/enpresa/edit.html.twig', [
            'enpresaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'admin_enpresa_delete')]
    public function delete(#[MapEntity(id: 'id')] Enpresa $enpresa)
    {
        if (!$enpresa) {
            $this->addFlash('error', 'messages.enpresa_ez_da_existitzen');
            return $this->redirectToRoute('admin_enpresa_list');
        }
        $eskakizunak = $this->eskakizunaRepo->findOneBy([
            'enpresa' => $enpresa,
        ]);
        if ($eskakizunak) {
            $this->addFlash('error', 'messages.ezin_enpresa_borratu_eskakizunak_dituelako');
            return $this->redirectToRoute('admin_enpresa_list');
        }
        $this->em->remove($enpresa);
        $this->em->flush();
        $this->addFlash('success', 'messages.enpresa_ezabatua');

        return $this->redirectToRoute('admin_enpresa_list');
    }

    #[Route(path: '/{id}', name: 'admin_enpresa_show')]
    public function show(#[MapEntity(id: 'id')] Enpresa $enpresa)
    {
        $this->logger->debug('EnpresaController->showAction->Enpresa->'.$enpresa->__toDebug());
        $form = $this->createForm(EnpresaFormType::class, $enpresa);
        return $this->render('admin/enpresa/show.html.twig', [
        'enpresaForm' => $form->createView(),
    ]);
    }

    #[Route(path: '/', name: 'admin_enpresa_list', options: ['expose' => true])]
    public function list()
    {
        $enpresak = $this->repo->findAll();
        return $this->render('admin/enpresa/list.html.twig', [
        'enpresak' => $enpresak,
    ]);
    }
}
