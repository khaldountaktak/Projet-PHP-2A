<?php

namespace App\Controller;

use App\Entity\Album;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AlbumRepository;
use Doctrine\Persistence\ManagerRegistry;


class AlbumController extends AbstractController
{
    #[Route('/album', name: 'app_album')]
    public function index(AlbumRepository $albumRepository): Response
    {
        $htmlpage= '<html>
        <body>Liste des <b> albums </b> de tous les membres :
          <ul>
';

      $albums= $albumRepository->findAll();

      foreach($albums as $album){
        $htmlpage .= '<li> <a href="' . $this->generateUrl('album_show', ['id' => $album->getId()]) . '">' . $album->getName() . ' </a> </li>';
      }
      

        // return $this->render('album/index.html.twig', [
        //     'controller_name' => 'AlbumController',
        // ]);

        $htmlpage .= '</ul>';

        $htmlpage .= '</body></html>';

        return new Response(
            $htmlpage,
            Response::HTTP_OK,
            array('content-type' => 'text/html')
            );
    }

  /**
 * Show a album
 *
 * @param Integer $id (note that the id must be an integer)
 */
#[Route('/album/{id}', name: 'album_show', requirements: ['id' => '\d+'])]
public function show(ManagerRegistry $doctrine, $id) : Response
{
        $albumRepo = $doctrine->getRepository(Album::class);
        $album = $albumRepo->find($id);

        if (!$album) {
                throw $this->createNotFoundException('The album does not exist');
        }

        $res = 'Le nom de cet album est ' . $album->getName();
        //...

        $res .= '<p/><a href="' . $this->generateUrl('app_album') . '">Back</a>';

        return new Response('<html><body>'. $res . '</body></html>');
}


}
