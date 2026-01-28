<?php

/**
 * Description of EskakizunMotaController.
 *
 * @author ibilbao
*/

namespace App\Controller;

use App\Entity\EskakizunMota;
use App\Form\EskakizunMotaFormType;
use App\Repository\EskakizunaRepository;
use App\Repository\EskakizunMotaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

 #[Route(path: '/{_locale}/admin/eskakizun_mota')]
class EskakizunMotaController extends AbstractController
{

    public function __construct (
        private readonly EntityManagerInterface $em,
        private readonly EskakizunMotaRepository $repo,
        private readonly EskakizunaRepository $eskakizunaRepo,
        private readonly LoggerInterface $logger,
    ) 
    {
    }

    #[Route(path: '/new', name: 'admin_eskakizun_mota_new')]
    public function new(Request $request)
    {
        $form = $this->createForm(EskakizunMotaFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $eskakizunmota = $form->getData();
            $this->em->persist($eskakizunmota);
            $this->em->flush();
            $this->addFlash('success', 'messages.eskakizun_mota_gordea');
            return $this->redirectToRoute('admin_eskakizun_mota_list');
        }

        return $this->render('admin/eskakizun_mota/new.html.twig', [
            'eskakizunMotaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'admin_eskakizun_mota_edit')]
    public function edit(Request $request, #[MapEntity(id: 'id')] EskakizunMota $eskakizunmota)
    {
        $form = $this->createForm(EskakizunMotaFormType::class, $eskakizunmota);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $eskakizunmota = $form->getData();
            $this->em->persist($eskakizunmota);
            $this->em->flush();
            $this->addFlash('success', 'messages.eskakizun_mota_gordea');
            return $this->redirectToRoute('admin_eskakizun_mota_list');
        }

        return $this->render('admin/eskakizun_mota/edit.html.twig', [
            'eskakizunMotaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'admin_eskakizun_mota_delete')]
    public function delete(#[MapEntity(id: 'id')] EskakizunMota $eskakizunMota)
    {
        if (!$eskakizunMota) {
            $this->addFlash('error', 'messages.eskakizun_mota_ez_da_existitzen');

            return $this->redirectToRoute('admin_eskakizun_mota_list');
        }

        $eskakizunak = $this->eskakizunaRepo->findOneBy([
            'eskakizunMota' => $eskakizunMota,
        ]);

        if ($eskakizunak) {
            $this->addFlash('error', 'messages.ezin_eskakizun_mota_borratu_eskakizunak_dituelako');
            return $this->redirectToRoute('admin_eskakizun_mota_list');
        }

        $this->em->remove($eskakizunMota);
        $this->em->flush();
        $this->addFlash('success', 'messages.eskakizun_mota_ezabatua');
        return $this->redirectToRoute('admin_eskakizun_mota_list');
    }

    #[Route(path: '/{id}', name: 'admin_eskakizun_mota_show')]
    public function show(#[MapEntity(id: 'id')] EskakizunMota $eskakizunmota)
    {
        $this->logger->debug('Showing: '.$eskakizunmota->getId());
        $form = $this->createForm(EskakizunMotaFormType::class, $eskakizunmota);

        return $this->render('admin/eskakizun_mota/show.html.twig', [
            'eskakizunMotaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/', name: 'admin_eskakizun_mota_list', options: ['expose' => true])]
    public function list()
    {
        $eskakizunmotak = $this->repo->findAll();

        return $this->render('admin/eskakizun_mota/list.html.twig', [
            'eskakizunmotak' => $eskakizunmotak,
        ]);
    }
}
