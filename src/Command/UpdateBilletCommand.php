<?php

namespace App\Command;

use App\Entity\Billet;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\BilletRepository;
use Doctrine\Persistence\ManagerRegistry;

#[AsCommand(
    name: 'app:update-billet',
    description: 'Updates a billet',
)]
class UpdateBilletCommand extends Command
{
    /**
     * @var BilletRepository
     */
    private $billetRepository;

    /**
     * @param ManagerRegistry
     */
    public function __construct(ManagerRegistry $doctrineManager)
    {
        $this->billetRepository = $doctrineManager->getRepository(Billet::class);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('billetId', InputArgument::REQUIRED, 'ID du billet à mettre à jour')
            ->addArgument('newPays', InputArgument::OPTIONAL, 'Nouveau pays du billet')
            ->addArgument('newValeur', InputArgument::OPTIONAL, 'Nouvelle valeur du billet')
            ->addArgument('newDateApparition', InputArgument::OPTIONAL, 'Nouvelle date d\'apparition du billet (au format AAAA-MM-JJ)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $billetId = $input->getArgument('billetId');
        $newPays = $input->getArgument('newPays');
        $newValeur = $input->getArgument('newValeur');
        $newDateApparition = $input->getArgument('newDateApparition');

        $billetToUpdate = $this->billetRepository->find($billetId);

        if ($billetToUpdate) {
            $billetToUpdate->setPays($newPays);
            $billetToUpdate->setValeur($newValeur);
            $billetToUpdate->setDateApparition($newDateApparition);
            
            $this->billetRepository->save($billetToUpdate, true);
            $io->success("Le billet a été mis à jour avec succès.");
            return Command::SUCCESS;
        } else {
            $io->error("Billet avec l'ID \"$billetId\" non trouvé !");
            return Command::FAILURE;
        }
    }
}
