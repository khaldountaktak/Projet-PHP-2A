<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Billet;

#[AsCommand(
    name: 'app:delete-billet',
    description: 'Supprime un billet existant d\'un album',
)]
class DeleteBilletCommand extends Command
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
            ->addArgument('billetId', InputArgument::REQUIRED, 'ID du billet à supprimer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $billetId = $input->getArgument('billetId');

        $billetRepository = $this->doctrineManager->getRepository(Billet::class);
        $billet = $billetRepository->find($billetId);

        if (!$billet) {
            $io->error("Le billet avec l'ID $billetId n'existe pas.");
            return Command::FAILURE;
        }

        $entityManager = $this->doctrineManager->getManager();
        $entityManager->remove($billet);
        $entityManager->flush();

        $io->success("Le billet avec l'ID $billetId a été supprimé.");

        return Command::SUCCESS;
    }
}
