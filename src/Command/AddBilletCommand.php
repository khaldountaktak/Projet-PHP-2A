<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Album;
use App\Entity\Billet;

#[AsCommand(
    name: 'app:add-billet',
    description: 'Créer un billet et l\'ajouter à un album',
)]
class AddBilletCommand extends Command
{
    /**
     * @var ManagerRegistry data access repository
     */
    private $doctrineManager;

    /**
     * Plugs the database to the command
     *
     * @param ManagerRegistry $doctrineManager
     */
    public function __construct(ManagerRegistry $doctrineManager)
    {
        $this->doctrineManager = $doctrineManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('billetPays', InputArgument::REQUIRED, 'Pays du billet à ajouter')
            ->addArgument('albumId', InputArgument::REQUIRED, 'ID de l\'album auquel sera associé ce billet')
            ->addArgument('valeur', InputArgument::REQUIRED, 'Valeur du billet')
            ->addArgument('dateApparition', InputArgument::REQUIRED, 'Date d\'apparition du billet (au format AAAA-MM-JJ)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $billetPays = $input->getArgument('billetPays');
        $albumId = $input->getArgument('albumId');
        $valeur = $input->getArgument('valeur');
        $dateApparition = $input->getArgument('dateApparition');
    
        $albumRepository = $this->doctrineManager->getRepository(Album::class); 
        $billetRepository = $this->doctrineManager->getRepository(Billet::class);
    
        $album = $albumRepository->find($albumId);
        if (!$album) {
            $io->error("L'album avec l'id $albumId n'existe pas");
            return Command::FAILURE;
        }
    
        $billet = new Billet();
        $billet->setPays($billetPays);
        $billet->setAlbum($album);
        $billet->setValeur($valeur);
        $billet->setDateApparition($dateApparition);
    
        $entityManager = $this->doctrineManager->getManager();
        $entityManager->persist($billet);
        $entityManager->flush();
    
        $io->success("Le billet de pays '$billetPays' avec la valeur '$valeur' et la date d'apparition '$dateApparition' a été ajouté à l'album avec l'id $albumId.");
    
        return Command::SUCCESS;
    }
}
