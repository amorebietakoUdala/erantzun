<?php

/**
 * Description of JatorriaController.
 *
 * @author ibilbao
 */ 

namespace App\Controller;

use App\Entity\Eskakizuna;
use App\Entity\Jatorria;
use App\Form\JatorriaFormType;
use App\Repository\EskakizunaRepository;
use App\Repository\JatorriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

 #[Route(path: '/{_locale}/admin/jatorria')]
class JatorriaController extends AbstractController
{

    public function __construct (
        private readonly EntityManagerInterface $em,
        private readonly JatorriaRepository $repo,
        private readonly EskakizunaRepository $eskakizunaRepo,
        private readonly LoggerInterface $logger,    
    ) 
    {
    }

    #[Route(path: '/new', name: 'admin_jatorria_new')]
    public function new(Request $request)
    {
        $form = $this->createForm(JatorriaFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Jatorria $jatorria */
            $jatorria = $form->getData();
            $this->em->persist($jatorria);
            $this->em->flush();
            $this->addFlash('success', 'messages.jatorria_gordea');
            return $this->redirectToRoute('admin_jatorria_list');
        }

        return $this->render('admin/jatorria/new.html.twig', [
            'jatorriaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'admin_jatorria_edit')]
    public function edit(Request $request, #[MapEntity(id: 'id')] Jatorria $jatorria)
    {
        $form = $this->createForm(JatorriaFormType::class, $jatorria);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $jatorria = $form->getData();
            $this->em->persist($jatorria);
            $this->em->flush();
            $this->addFlash('success', 'messages.jatorria_gordea');
            return $this->redirectToRoute('admin_jatorria_list');
        }

        return $this->render('admin/jatorria/edit.html.twig', [
            'jatorriaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'admin_jatorria_delete')]
    public function delete(#[MapEntity(id: 'id')] Jatorria $jatorria)
    {
        if (!$jatorria) {
            $this->addFlash('error', 'messages.jatorria_ez_da_existitzen');
            return $this->redirectToRoute('admin_jatorria_list');
        }
        $eskakizunak = $this->eskakizunaRepo->findOneBy([
            'jatorria' => $jatorria,
        ]);
        if ($eskakizunak) {
            $this->addFlash('error', 'messages.ezin_jatorria_borratu_eskakizunak_dituelako');
            return $this->redirectToRoute('admin_jatorria_list');
        }
        $this->em->remove($jatorria);
        $this->em->flush();
        $this->addFlash('success', 'messages.jatorria_ezabatua');
        return $this->redirectToRoute('admin_jatorria_list');
    }

    #[Route(path: '/{id}', name: 'admin_jatorria_show')]
    public function show(#[MapEntity(id: 'id')] Jatorria $jatorria)
    {
        $this->logger->debug('Showing: '.$jatorria->getId());
        $form = $this->createForm(JatorriaFormType::class, $jatorria);

        return $this->render('admin/jatorria/show.html.twig', [
            'jatorriaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/', name: 'admin_jatorria_list', options: ['expose' => true])]
    public function list()
    {
        $jatorriak = $this->repo->findAll();
        return $this->render('admin/jatorria/list.html.twig', [
            'jatorriak' => $jatorriak,
        ]);
    }
}
