<?php

namespace App\Controller;

use App\Entity\Exposition;
use App\Entity\Billet;
use App\Form\ExpositionType;
use App\Repository\ExpositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/exposition')]
final class ExpositionController extends AbstractController
{

    #[Route('/{exposition_id}/billet/{billet_id}', name: 'app_exposition_billet_show', methods: ['GET'])]
    public function billetShow(
        #[MapEntity(id: 'exposition_id')] Exposition $exposition,
        #[MapEntity(id: 'billet_id')] Billet $billet
    ): Response {
        // Vérifie si le billet appartient bien à l'exposition
        if (!$exposition->getBillets()->contains($billet)) {
            throw $this->createNotFoundException("Couldn't find such a billet in this exposition!");
        }
    
        return $this->render('exposition/billetshow.html.twig', [
            'billet' => $billet,
            'exposition' => $exposition,
        ]);
    }

    #[Route(name: 'app_exposition_index', methods: ['GET'])]
    public function index(ExpositionRepository $expositionRepository): Response
    {
        return $this->render('exposition/index.html.twig', [
            'expositions' => $expositionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_exposition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exposition = new Exposition();
        $form = $this->createForm(ExpositionType::class, $exposition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exposition);
            $entityManager->flush();

            return $this->redirectToRoute('app_exposition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exposition/new.html.twig', [
            'exposition' => $exposition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_exposition_show', methods: ['GET'])]
    public function show(Exposition $exposition): Response
    {
        return $this->render('exposition/show.html.twig', [
            'exposition' => $exposition,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_exposition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Exposition $exposition, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExpositionType::class, $exposition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_exposition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exposition/edit.html.twig', [
            'exposition' => $exposition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_exposition_delete', methods: ['POST'])]
    public function delete(Request $request, Exposition $exposition, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$exposition->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($exposition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_exposition_index', [], Response::HTTP_SEE_OTHER);
    }
}
