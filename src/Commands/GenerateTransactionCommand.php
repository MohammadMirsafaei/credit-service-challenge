<?php

namespace App\Commands;

use App\DTOs\Transaction\CreateTransactionDto;
use App\Enums\TransactionType;
use App\Services\TransactionService;
use Assert\Assertion;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateTransactionCommand extends Command
{
    public function __construct(
        private TransactionService $transactionService
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('transaction:generate')
            ->addArgument('user_id', InputArgument::REQUIRED)
            ->addArgument('amount', InputArgument::REQUIRED)
            ->addArgument('type', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->validate($input);
        try {
            $this->transactionService->createTransaction(
                new CreateTransactionDto(
                    $input->getArgument('user_id'),
                    $input->getArgument('amount'),
                    TransactionType::from($input->getArgument('type'))
                )
            );
        } catch (\Throwable $e) {
            $output->writeln('Creation failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

    private function validate(InputInterface $input)
    {
        Assertion::integerish($input->getArgument('user_id'));
        Assertion::integerish($input->getArgument('amount'));
        Assertion::greaterThan(intval($input->getArgument('amount')), 0);
        Assertion::notNull(TransactionType::tryFrom($input->getArgument('type')));
    }
}