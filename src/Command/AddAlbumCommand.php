<?php

namespace App\Command;

use App\Entity\Album;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\AlbumRepository;

#[AsCommand(
    name: 'app:add-album',
    description: 'Adds a new album',
)]
class AddAlbumCommand extends Command
{
        /**
         *  @var AlbumRepository data access repository
         */ 
        private $albumRepository;

        /**
         * Plugs the database to the command
         *
         * @param ManagerRegistry $doctrineManager
         */
    public function __construct(ManagerRegistry $doctrineManager)
    {
        $this->albumRepository = $doctrineManager->getRepository(Album::class);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('albumName', InputArgument::REQUIRED, 'Le nom de l album')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $albumName = $input->getArgument('albumName');

        $album = new Album();
        $album->setName($albumName);

        $this->albumRepository->save($album,true);

        if ($album->getId()){
            $io->success('Created: '. $albumName);
                return Command::SUCCESS;
        }
        else {
                $io->error('could not create album!');
                return Command::FAILURE;
        }
        
    }
}
