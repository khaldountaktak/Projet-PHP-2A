<?php

namespace App\Controller;

use App\Entity\Billet;
use App\Form\BilletType;
use App\Repository\BilletRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Album; 

#[Route('/billet')]
final class BilletController extends AbstractController
{
    #[Route(name: 'app_billet_index', methods: ['GET'])]
    public function index(BilletRepository $billetRepository): Response
    {
        return $this->render('billet/index.html.twig', [
            'billets' => $billetRepository->findAll(),
        ]);
    }

    #[Route('/billet/new/{id}', name: 'app_billet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Album $album): Response
    {
        // Step 1: Create a new Billet and set its Album
        $billet = new Billet();
        $billet->setAlbum($album);

        // Step 2: Create the form and handle the request
        $form = $this->createForm(BilletType::class, $billet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Step 3: Persist the new Billet and redirect to the album page
            $entityManager->persist($billet);
            $entityManager->flush();

            return $this->redirectToRoute('app_album_show', [
                'id' => $album->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('billet/new.html.twig', [
            'billet' => $billet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_billet_show', methods: ['GET'])]
    public function show(Billet $billet): Response
    {
        return $this->render('billet/show.html.twig', [
            'billet' => $billet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_billet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Billet $billet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BilletType::class, $billet);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            return $this->redirectToRoute('album_show', [
                'id' => $billet->getAlbum()->getId()
            ], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('billet/edit.html.twig', [
            'billet' => $billet,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_billet_delete', methods: ['POST'])]
    public function delete(Request $request, Billet $billet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$billet->getId(), $request->getPayload()->getString('_token'))) {
            $albumId = $billet->getAlbum()->getId(); 
            $entityManager->remove($billet);
            $entityManager->flush();
    
            return $this->redirectToRoute('album_show', [
                'id' => $albumId
            ], Response::HTTP_SEE_OTHER);
        }
    
        return $this->redirectToRoute('app_billet_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
