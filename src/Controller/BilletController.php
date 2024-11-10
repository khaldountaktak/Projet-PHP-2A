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
    #[Route('/', name: 'app_billet_index', methods: ['GET'])]
    public function index(BilletRepository $billetRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login'); // Adjust 'app_login' to your actual login route
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $billets = $billetRepository->findAll();
        } else {
            $member = $this->getUser();
            $billets = $billetRepository->findMemberBillets($member);
        }

        return $this->render('billet/index.html.twig', [
            'billets' => $billets,
        ]);
    }

    #[Route('/billet/new/{id}', name: 'app_billet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Album $album): Response
    {
        $billet = new Billet();
        $billet->setAlbum($album);
    
        $form = $this->createForm(BilletType::class, $billet);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Synchronize the relationship explicitly
            foreach ($billet->getExpositions() as $exposition) {
                $exposition->addBillet($billet);
            }
    
            $entityManager->persist($billet);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_album', [
                'id' => $album->getId(),
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
    
            // Rediriger vers la page de l'album associé au billet après modification
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
        // Vérifier le token CSRF pour sécuriser la suppression
        if ($this->isCsrfTokenValid('delete' . $billet->getId(), $request->get('_token'))) {
            // Récupérer l'ID de l'album avant de supprimer le billet
            $albumId = $billet->getAlbum()->getId();
            
            // Supprimer le billet
            $entityManager->remove($billet);
            $entityManager->flush();
    
            // Redirection vers la page de l'album associé après suppression
            return $this->redirectToRoute('album_show', [
                'id' => $albumId
            ], Response::HTTP_SEE_OTHER);
        }
    
        // Redirection en cas d'échec du token CSRF
        return $this->redirectToRoute('app_billet_index', [], Response::HTTP_SEE_OTHER);
    }
    
    
}
