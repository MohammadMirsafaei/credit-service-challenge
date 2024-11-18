<?php

namespace App\Commands;

use App\DTOs\Transaction\UserReportDto;
use App\Services\TransactionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UsersTransactionsReportCommand extends Command
{
    public function __construct(
        private TransactionService $transactionService
    )
    {
        parent::__construct();
    }
    protected function configure()
    {
        $this->setName('transaction:report:users');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $results = $this->transactionService->reportOfUsers();
        foreach ($results as $result) {
            $output->writeln("{$result->date->format('Y-m-d')}: {$result->totalAmount}");
        }
        return Command::SUCCESS;
    }
}