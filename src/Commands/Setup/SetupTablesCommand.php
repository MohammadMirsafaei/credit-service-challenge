<?php

namespace App\Commands\Setup;

use App\Services\DatabaseService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupTablesCommand extends Command
{
    public function __construct(private DatabaseService $databaseService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('setup:database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->databaseService->execute("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(100) NOT NULL,
                credit INT UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ");

        $this->databaseService->execute("
            CREATE TABLE IF NOT EXISTS transactions (
                id INT AUTO_INCREMENT NOT NULL,
                amount INT UNSIGNED NOT NULL DEFAULT 0,
                `type` ENUM('credit', 'deposit'),
                user_id INT NOT NULL,
                created_at DATETIME NOT NULL,
                PRIMARY KEY(id),
                FOREIGN KEY (user_id) REFERENCES users(id),
                INDEX idx_created_at (created_at),
                INDEX idx_created_at_date ((DATE(created_at)))
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
        ");
        return Command::SUCCESS;
    }
}