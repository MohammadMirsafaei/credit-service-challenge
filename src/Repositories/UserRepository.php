<?php

namespace App\Repositories;

use App\DTOs\User\CreateUserDto;
use App\Models\User;
use App\Services\DatabaseService;

class UserRepository
{
    public function __construct(
        private readonly DatabaseService $databaseService,
    )
    {
    }

    public function beginTransaction(): void
    {
        $this->databaseService->getConnection()->beginTransaction();
    }

    public function commit(): void
    {
        $this->databaseService->getConnection()->commit();
    }

    public function rollback(): void
    {
        $this->databaseService->getConnection()->rollBack();
    }

    public function createUser(CreateUserDto $dto): User
    {
        $id = $this->databaseService->insert(
            'INSERT INTO users (name, credit) VALUES (?, ?)',
            [
                $dto->name,
                $dto->credit
            ]
        );

        return new User(
            $id,
            $dto->name,
            $dto->credit
        );
    }

    public function findById(int $id): ?User
    {
        $result = $this->databaseService->query('SELECT * FROM users WHERE id = ?', [$id]);
        if (empty($result)) {
            return null;
        }
        return new User(
            current($result)['id'],
            current($result)['name'],
            current($result)['credit']
        );
    }
    public function getUserCreditForUpdate(int $userId): int
    {
        return current($this->databaseService->query('SELECT credit FROM users WHERE id = ? FOR UPDATE', [$userId]))['credit'];
    }

    public function updateCredit(int $id, int $amount): void
    {
        $this->databaseService->execute('UPDATE users SET credit = credit + ? WHERE id = ?', [$amount, $id]);
    }
}