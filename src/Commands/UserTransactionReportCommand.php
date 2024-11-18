<?php

namespace App\Commands;

use App\DTOs\Transaction\UserReportDto;
use App\Services\TransactionService;
use Assert\Assertion;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserTransactionReportCommand extends Command
{
    public function __construct(
        private TransactionService $transactionService
    )
    {
        parent::__construct();
    }
    protected function configure()
    {
        $this->setName('transaction:report:user')
            ->addArgument('user_id', InputArgument::REQUIRED)
            ->addArgument('start', InputArgument::OPTIONAL)
            ->addArgument('end', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = $input->getArgument('user_id');
        Assertion::integerish($userId);
        $start = $input->getArgument('start');
        $end = $input->getArgument('end');
        if (!empty($start)) {
            Assertion::date($start, 'Y-m-d');
            $start = Carbon::parse($start);
        }
        if (!empty($end)) {
            Assertion::date($end, 'Y-m-d');
            $end = Carbon::parse($end);
        }
        $results = $this->transactionService->reportOfUser(new UserReportDto(
            $userId,
            $start,
            $end
        ));
        foreach ($results as $result) {
            $output->writeln("{$result->date->format('Y-m-d')}: {$result->totalAmount}");
        }
        return Command::SUCCESS;
    }
}