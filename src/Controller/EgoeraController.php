<?php

/**
 * Description of EgoeraController.
 *
 * @author ibilbao
*/

namespace App\Controller;

use App\Entity\Egoera;
use App\Form\EgoeraFormType;
use App\Repository\EgoeraRepository;
use App\Repository\EskakizunaRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

 #[Route(path: '/{_locale}/admin/egoera')]
class EgoeraController extends AbstractController
{

    public function __construct (
        private readonly EntityManagerInterface $em,
        private readonly EgoeraRepository $repo,
        private readonly EskakizunaRepository $eskakizunaRepo,
    ) 
    {
    }

    #[Route(path: '/new', name: 'admin_egoera_new')]
    public function new(Request $request)
    {
        $form = $this->createForm(EgoeraFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Egoera $egoera */
            $egoera = $form->getData();
            $this->em->persist($egoera);
            $this->em->flush();
            $this->addFlash('success', 'messages.egoera_gordea');
            return $this->redirectToRoute('admin_egoera_list');
        }

        return $this->render('admin/egoera/new.html.twig', [
            'egoeraForm' => $form,
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'admin_egoera_edit')]
    public function edit(Request $request, Egoera $egoera)
    {
        $form = $this->createForm(EgoeraFormType::class, $egoera);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Egoera $data */
            $egoera = $form->getData();
            $this->em->persist($egoera);
            $this->em->flush();
            $this->addFlash('success', 'messages.egoera_gordea');
            return $this->redirectToRoute('admin_egoera_list');
        }

        return $this->render('admin/egoera/edit.html.twig', [
            'egoeraForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'admin_egoera_delete')]
    public function delete(Egoera $egoera)
    {
        if (!$egoera) {
            $this->addFlash('error', 'messages.egoera_ez_da_existitzen');
            return $this->redirectToRoute('admin_egoera_list');
        }

        $eskakizunak = $this->eskakizunaRepo->findOneBy([
            'egoera' => $egoera,
        ]);

        if ($eskakizunak) {
            $this->addFlash('error', 'messages.ezin_egoera_borratu_eskakizunak_dituelako');
            return $this->redirectToRoute('admin_egoera_list');
        }
        $this->em->remove($egoera);
        $this->em->flush();
        $this->addFlash('success', 'messages.egoera_ezabatua');

        return $this->redirectToRoute('admin_egoera_list');
    }

    #[Route(path: '/{id}', name: 'admin_egoera_show')]
    public function show(Egoera $egoera, LoggerInterface $logger)
    {
        $logger->debug('EgoeraController->showAction->Egoera->'.$egoera->__toDebug());
        $form = $this->createForm(EgoeraFormType::class, $egoera);
        $logger->debug('EgoeraController->showAction->Egoera->render: admin/egoera/show.html.twig');

        return $this->render('admin/egoera/show.html.twig', [
            'egoeraForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/', name: 'admin_egoera_list', options: ['expose' => true])]
    public function list()
    {
        $egoerak = $this->repo->findAll();
        return $this->render('admin/egoera/list.html.twig', [
            'egoerak' => $egoerak,
        ]);
    }
}
