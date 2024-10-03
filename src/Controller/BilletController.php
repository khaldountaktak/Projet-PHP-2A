<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Billet;

class BilletController extends AbstractController
{
    #[Route('/billet/{id}', name: 'app_billet', requirements: ['id' => '\d+'])]
    public function show(ManagerRegistry $doctrine, $id): Response
    {
        $billetRepo = $doctrine->getRepository(Billet::class);
        $billet = $billetRepo->find($id);

        return $this->render('billet/index.html.twig', [
            'billet' => $billet,
        ]);
    }
}
