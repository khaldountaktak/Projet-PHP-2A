<?php

namespace App\Controller;

use App\Entity\Exposition;
use App\Entity\Billet;
use App\Form\ExpositionType;
use App\Entity\Member;
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
        if (!$exposition->getBillets()->contains($billet)) {
            throw $this->createNotFoundException("Couldn't find such a billet in this exposition!");
        }
    
        $hasAccess = false;
        if ($this->isGranted('ROLE_ADMIN') || $exposition->isPubliee()) {
            $hasAccess = true;
        } else {
            $member = $this->getUser();
            if ($member && ($member === $exposition->getMember())) {
                $hasAccess = true;
            }
        }
    
        if (!$hasAccess) {
            throw $this->createAccessDeniedException("You cannot access the requested resource!");
        }
    
        return $this->render('exposition/billet show.html.twig', [
            'billet' => $billet,
            'exposition' => $exposition
        ]);
    }
    #[Route('/', name: 'app_exposition_index', methods: ['GET'])]
    public function index(ExpositionRepository $expositionRepository): Response
    {
        $member = $this->getUser();
        $publicExpositions = $expositionRepository->findBy(['publiee' => true]);

        $privateExpositions = [];
        if ($member) {
            $privateExpositions = $expositionRepository->findBy([
                'publiee' => false,
                'member' => $member,
            ]);
        }

        return $this->render('exposition/index.html.twig', [
            'public_expositions' => $publicExpositions,
            'private_expositions' => $privateExpositions,
        ]);
    }
    // #[Route('/new', name: 'app_exposition_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $exposition = new Exposition();
    //     $form = $this->createForm(ExpositionType::class, $exposition);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($exposition);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_exposition_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('exposition/new.html.twig', [
    //         'exposition' => $exposition,
    //         'form' => $form,
    //     ]);
    // }
    #[Route('/new/{memberId}', name: 'app_exposition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[MapEntity(id: 'memberId')] Member $member): Response
    {
        // Create a new Exposition and associate it with the given Member
        $exposition = new Exposition();
        $exposition->setMember($member);

        // Create and handle the form
        $form = $this->createForm(ExpositionType::class, $exposition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($exposition);
            $entityManager->flush();

            // Redirect to the member's profile page after creation
            return $this->redirectToRoute('app_member_show', [
                'id' => $member->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exposition/new.html.twig', [
            'exposition' => $exposition,
            'form' => $form,
            'member' => $member,
        ]);
    }

    #[Route('/{id}', name: 'app_exposition_show', methods: ['GET'])]
    public function show(Exposition $exposition): Response
    {
            $hasAccess = false;
            if($this->isGranted('ROLE_ADMIN') || $exposition->isPubliee()) {
                    $hasAccess = true;
            }
            else {
                    $member = $this->getUser();
                    if ( $member &&  ($member == $exposition->getMember()) ) {
                        $hasAccess = true;
                    }
            }
            if(! $hasAccess) {
                    throw $this->createAccessDeniedException("You cannot access the requested resource!");
            }
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

            // Redirect to the member's profile page after editing
            return $this->redirectToRoute('app_member_show', [
                'id' => $exposition->getMember()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exposition/edit.html.twig', [
            'exposition' => $exposition,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_exposition_delete', methods: ['POST'])]
    public function delete(Request $request, Exposition $exposition, EntityManagerInterface $entityManager): Response
    {
        $memberId = $exposition->getMember()->getId();
        $hasAccess = false;
    
        // Check if the user has access to delete the exposition
        if ($this->isGranted('ROLE_ADMIN') || $this->getUser() === $exposition->getMember()) {
            $hasAccess = true;
        }
    
        if (!$hasAccess) {
            throw $this->createAccessDeniedException("You cannot delete this exposition!");
        }
    
        // Check the CSRF token for security
        if ($this->isCsrfTokenValid('delete' . $exposition->getId(), $request->get('_token'))) {
            $entityManager->remove($exposition);
            $entityManager->flush();
        }
    
        // Redirect to the member's profile page after deletion
        return $this->redirectToRoute('app_member_show', [
            'id' => $memberId,
        ], Response::HTTP_SEE_OTHER);
    }
    
}
