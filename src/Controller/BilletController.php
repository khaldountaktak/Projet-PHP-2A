<?php

namespace App\Controller;

use App\Entity\Billet;
use App\Entity\Album;
use App\Form\BilletType;
use App\Repository\BilletRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/billet')]
#[IsGranted('ROLE_USER')] // Ensure all routes require at least ROLE_USER
final class BilletController extends AbstractController
{
    #[Route('/', name: 'app_billet_index', methods: ['GET'])]
    public function index(BilletRepository $billetRepository, MemberRepository $memberRepository): Response
    {
        $user = $memberRepository->findOneBy(["email" => $this->getUser()->getUserIdentifier()]); 

        if ($this->isGranted('ROLE_ADMIN')) {
            // Admins can see all billets
            $billets = $billetRepository->findAll();
        } else {
            // Regular members can see only their own billets
            $billets = $billetRepository->findBy(['album' => $user->getAlbum()]);
        }

        return $this->render('billet/index.html.twig', [
            'billets' => $billets,
        ]);
    }

    #[Route('/new/{id}', name: 'app_billet_new', methods: ['GET', 'POST'])]
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
        // Ensure the user can only view their own billets, unless they are an admin
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $billet->getAlbum()->getMember()) {
            throw $this->createAccessDeniedException("You do not have permission to view this billet.");
        }

        return $this->render('billet/show.html.twig', [
            'billet' => $billet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_billet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Billet $billet, EntityManagerInterface $entityManager): Response
    {
        // Allow only admins or the owner of the billet to edit it
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $billet->getAlbum()->getMember()) {
            throw $this->createAccessDeniedException("You do not have permission to edit this billet.");
        }

        $form = $this->createForm(BilletType::class, $billet);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            // Redirect to the associated album page after editing
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
        // Allow only admins or the owner of the billet to delete it
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $billet->getAlbum()->getMember()) {
            throw $this->createAccessDeniedException("You do not have permission to delete this billet.");
        }

        // Verify CSRF token for secure deletion
        if ($this->isCsrfTokenValid('delete' . $billet->getId(), $request->get('_token'))) {
            $albumId = $billet->getAlbum()->getId();
            
            $entityManager->remove($billet);
            $entityManager->flush();
    
            // Redirect to the associated album page after deletion
            return $this->redirectToRoute('album_show', [
                'id' => $albumId
            ], Response::HTTP_SEE_OTHER);
        }
    
        // Redirect in case of CSRF token failure
        return $this->redirectToRoute('app_billet_index', [], Response::HTTP_SEE_OTHER);
    }
}
