<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
{        /**
    *  @var ManagerRegistry data access repository
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
            ->addArgument('albumId', InputArgument::REQUIRED, 'ID de l album auquel sera associé ce billet')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $billetPays = $input->getArgument('billetPays');
        $albumId = $input->getArgument('albumId');
    
        $albumRepository = $this->doctrineManager->getRepository(Album::class); 
        $billetRepository = $this->doctrineManager->getRepository(Billet::class);
    
        $album = $albumRepository->find($albumId);
        if (!$album){
            $io->error("L'album avec l'id $albumId n'existe pas");
            return Command::FAILURE;
        }
    
        $billet = new Billet();
        $billet->setPays($billetPays);
        $billet->setAlbum($album); 
    
        $entityManager = $this->doctrineManager->getManager();
        $entityManager->persist($billet);
        $entityManager->flush();
    
        $io->success("Le billet '$billetPays' a été ajouté à l'album avec l'id $albumId.");
    
        return Command::SUCCESS;
    }
    
}
