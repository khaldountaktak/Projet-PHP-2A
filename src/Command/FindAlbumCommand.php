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
    name: 'app:find-album',
    description: 'Returns all albums if used without an id argument; will return the album with a specific id else',
)]
class FindAlbumCommand extends Command
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
            ->addArgument('id', InputArgument::OPTIONAL, 'Id of the album to find')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');
        dump($id);
        if (!$id){
            $albums = $this->albumRepository->findAll();
            $io->title('list of albums:');
    
            $io->listing($albums);
            return Command::SUCCESS;
        } else {
            $albums = $this->albumRepository->find($id);
            dump($albums);
            $io->success("Album found is" .$albums->getName());
            return Command::SUCCESS;

        }

        return Command::FAILURE;
    }
}
