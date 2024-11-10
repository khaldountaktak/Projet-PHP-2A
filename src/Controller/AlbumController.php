<?php

namespace App\Controller;

use App\Entity\Album;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AlbumRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\MemberRepository;


class AlbumController extends AbstractController
{
    #[Route('/album', name: 'app_album')]
    public function index(AlbumRepository $albumRepository, MemberRepository $memberRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {

            $albums = $albumRepository->findAll();
        } else {

            $email = $this->getUser()->getUserIdentifier();
            $member = $memberRepository->findOneBy(['email' => $email]);
            
            if (!$member) {
                throw $this->createNotFoundException("Member not found.");
            }
            dump($member->getAlbum());
            $albums = $member->getAlbum();
        }

        return $this->render('album/index.html.twig', [
            'albums' => $albums
        ]);
    }


  /**
 * Show a album
 *
 * @param Integer $id (note that the id must be an integer)
 */
#[Route('/album/{id}', name: 'album_show', requirements: ['id' => '\d+'])]
public function show(ManagerRegistry $doctrine, $id): Response
{
    $albumRepo = $doctrine->getRepository(Album::class);
    $album = $albumRepo->find($id);

    if (!$album) {
        throw $this->createNotFoundException('The album does not exist');
    }

    // Vérification de l'accès : propriétaire ou administrateur
    $hasAccess = $this->isGranted('ROLE_ADMIN') || ($this->getUser() === $album->getMember());

    if (!$hasAccess) {
        throw $this->createAccessDeniedException("You cannot access another member's album!");
    }

    return $this->render('album/show.html.twig', [
        'album' => $album
    ]);
}



}
