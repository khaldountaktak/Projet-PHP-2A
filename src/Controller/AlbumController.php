<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AlbumRepository;


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
        $htmlpage .= '<li>' . $album->getName() . '</li>';
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

    
}
