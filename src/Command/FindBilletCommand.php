<?php

namespace App\Command;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\BilletRepository;
use App\Entity\Billet;

#[AsCommand(
    name: 'app:find-billet',
    description: 'Finds a billet by its id',
)]
class FindBilletCommand extends Command
{
    /**
     * @var BilletRepository data access repository
     */
    private $billetRepository;

    /**
     * @param ManagerRegistry $doctrineManager
     */
    public function __construct(ManagerRegistry $doctrineManager)
    {
        $this->billetRepository = $doctrineManager->getRepository(Billet::class);
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->addArgument('billetId', InputArgument::REQUIRED, 'ID du billet à chercher')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $billetId = $input->getArgument('billetId');

        $billet = $this->billetRepository->find($billetId);

        if (!$billet){
            $io->error("Le billet avec l'id $billetId n'existe pas");
            return Command::FAILURE;
        } else {
            $io->success("Le billet trouvé est " . $billet->getPays());
        }
        return Command::SUCCESS;
    }
}
