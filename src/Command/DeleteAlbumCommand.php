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
use App\Repository\AlbumRepository;
use Doctrine\Persistence\ManagerRegistry;

#[AsCommand(
    name: 'app:delete-album',
    description: 'Deletes an album with a given ID',
)]


class DeleteAlbumCommand extends Command
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
        $this->albumRepository = $doctrineManager -> getRepository(Album::class);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('albumId', InputArgument::REQUIRED, 'album ID to be deleted')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $albumId = $input->getArgument('albumId');

        $album = $this->albumRepository->find($albumId);

        if ($album){
            $this->albumRepository->remove($album,true);
            $io->success('Deletion completed.');
            
            return Command::SUCCESS;
        } else {
            $io->error('no todos found with id "'. $albumId .'"!');
            return Command::FAILURE;
        }
        
     
    }
}
