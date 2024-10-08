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
            ->addArgument('billetId', InputArgument::REQUIRED, 'ID of the billet to update')
            ->addArgument('newPays', InputArgument::REQUIRED, 'New country (pays) for the billet');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $billetId = $input->getArgument('billetId');
        $newPays = $input->getArgument('newPays');

        $billetToUpdate = $this->billetRepository->find($billetId);

        if ($billetToUpdate) {
            $billetToUpdate->setPays($newPays);
            $this->billetRepository->save($billetToUpdate, true);
            $io->success('Billet mis a jour.');
            return Command::SUCCESS;
        } else {
            $io->error('Billet avec id non trouvé "' . $billetId . '"!');
            return Command::FAILURE;
        }
    }
}
