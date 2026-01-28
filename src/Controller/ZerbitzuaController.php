<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use App\Entity\Zerbitzua;
use App\Form\ZerbitzuaFormType;
use App\Repository\ZerbitzuaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/{_locale}/admin/zerbitzua')]
class ZerbitzuaController extends AbstractController
{
    public function __construct (
        private readonly EntityManagerInterface $em,
        private readonly ZerbitzuaRepository $repo,
        private readonly LoggerInterface $logger,
    ) 
    {
    }

    #[Route(path: '/new', name: 'admin_zerbitzua_new')]
    public function new(Request $request)
    {
        $form = $this->createForm(ZerbitzuaFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $zerbitzua = $form->getData();
            $this->em->persist($zerbitzua);
            $this->em->flush();
            $this->addFlash('success', 'messages.zerbitzua_gordea');
            return $this->redirectToRoute('admin_zerbitzua_list');
        }

        return $this->render('admin/zerbitzua/new.html.twig', [
            'zerbitzuaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'admin_zerbitzua_edit')]
    public function edit(Request $request, #[MapEntity(id: 'id')] Zerbitzua $zerbitzua)
    {
        $form = $this->createForm(ZerbitzuaFormType::class, $zerbitzua);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $zerbitzua = $form->getData();
            $this->em->persist($zerbitzua);
            $this->em->flush();
            $this->addFlash('success', 'messages.zerbitzua_gordea');
            return $this->redirectToRoute('admin_zerbitzua_list');
        }

        return $this->render('admin/zerbitzua/edit.html.twig', [
            'zerbitzuaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'admin_zerbitzua_delete')]
    public function delete($id)
    {
        $zerbitzua = $this->repo->find($id);
        if (!$zerbitzua) {
            $this->addFlash('error', 'messages.zerbitzua_ez_da_existitzen');
            return $this->redirectToRoute('admin_zerbitzua_list');
        }
        $this->em->remove($zerbitzua);
        $this->em->flush();
        $this->addFlash('success', 'messages.zerbitzua_ezabatua');
        return $this->redirectToRoute('admin_zerbitzua_list');
    }

    #[Route(path: '/{id}', name: 'admin_zerbitzua_show', options: ['expose' => true])]
    public function show(#[MapEntity(id: 'id')] Zerbitzua $zerbitzua)
    {
        $this->logger->debug('Showing: '.$zerbitzua->getId());
        $form = $this->createForm(ZerbitzuaFormType::class, $zerbitzua);

        return $this->render('admin/zerbitzua/show.html.twig', [
            'zerbitzuaForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/', name: 'admin_zerbitzua_list', options: ['expose' => true])]
    public function list()
    {
        $zerbitzuak = $this->repo->findAll();
        return $this->render('admin/zerbitzua/list.html.twig', [
            'zerbitzuak' => $zerbitzuak,
        ]);
    }
}
