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
    name: 'app:update-album',
    description: 'Updates an album',
)]
class UpdateAlbumCommand extends Command
{
    /**
     * @var AlbumRepository
     */
    private $albumRepository;

    /**
     * @param ManagerRegistry
     */
    public function __construct(ManagerRegistry $doctrineManager)
    {
        $this->albumRepository = $doctrineManager->getRepository(Album::class);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('albumId', InputArgument::REQUIRED, 'ID of the album to update')
            ->addArgument('albumNewName', InputArgument::REQUIRED, 'New album name')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $albumId = $input->getArgument('albumId');
        $albumNewName = $input->getArgument('albumNewName');

        $albumToUpdate = $this->albumRepository->find($albumId);

        if ($albumToUpdate){
            $albumToUpdate->setName($albumNewName);
            $this->albumRepository->save($albumToUpdate,true);
            $io->success('Update completed.');
            return Command::SUCCESS;
        }
        else {
            $io->error('no albums found with id "'. $albumId .'"!');
            return Command::FAILURE;
        }
    }
}
