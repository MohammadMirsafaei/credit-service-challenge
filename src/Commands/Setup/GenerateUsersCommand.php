<?php

namespace App\Commands\Setup;

use App\DTOs\User\CreateUserDto;
use App\Services\UserService;
use Assert\Assertion;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateUsersCommand extends Command
{
    public function __construct(private UserService $userService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('setup:generate:users')
            ->addArgument('count', InputArgument::REQUIRED, 'Count of the users you want to generate');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $input->getArgument('count');
        Assertion::integerish($count);
        $count = intval($count);

        $faker = Factory::create();
        for ($i = 0; $i < $count; $i++) {
            $this->userService->createUser(
                new CreateUserDto(
                    $faker->name(),
                    $faker->numberBetween(0, 1_000_000)
                )
            );
        }
        return Command::SUCCESS;
    }
}