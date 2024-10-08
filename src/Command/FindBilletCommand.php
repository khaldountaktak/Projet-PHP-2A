<?php

namespace App\Command;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\BilletRepository;
use App\Entity\Billet;

#[AsCommand(
    name: 'app:find-billet',
    description: 'Retourne des billets avec le critere de recherche ou tous les billets si aucun critere de recherche n\'a été défini',
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
            ->addOption('pays', null, InputArgument::OPTIONAL, 'Pays du billet à chercher')
            ->addOption('valeur', null, InputArgument::OPTIONAL, 'Valeur du billet à chercher');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pays = $input->getOption('pays');
        $valeur = $input->getOption('valeur');

        $criteria = [];
        if ($pays) {
            $criteria['pays'] = $pays;
        }
        if ($valeur) {
            $criteria['valeur'] = $valeur;
        }

        if (empty($criteria)) {
            $billets = $this->billetRepository->findAll();
        } else {
            $billets = $this->billetRepository->findBy($criteria);
        }

        if (empty($billets)) {
            $io->error("Aucun billet trouvé.");
            return Command::FAILURE;
        }

        foreach ($billets as $billet) {
            $io->success("Billet trouvé: ID - " . $billet->getId() . ", Pays - " . $billet->getPays() . ", Valeur - " . $billet->getValeur());
        }

        return Command::SUCCESS;
    }
}
