<?php

namespace App\Repositories;

use App\DTOs\Transaction\CreateTransactionDto;
use App\DTOs\Transaction\UserReportDto;
use App\DTOs\Transaction\UsersTransactionReportDto;
use App\DTOs\Transaction\UserTransactionReportDto;
use App\Models\Transaction;
use App\Services\DatabaseService;
use Carbon\Carbon;

class TransactionRepository
{
    public function __construct(
        private readonly DatabaseService $databaseService,
    )
    {
    }

    public function create(CreateTransactionDto $dto): Transaction
    {
        $now = Carbon::now();
        $id = $this->databaseService->insert(
            'INSERT INTO transactions (amount, type, user_id, created_at) VALUES (?, ?, ?, ?)',
            [
                $dto->amount,
                $dto->type->value,
                $dto->userId,
                $now
            ]
        );
        return new Transaction(
            $id,
            $dto->amount,
            $dto->type,
            $dto->userId,
            $now
        );
    }

    /**
     * @param UserReportDto $dto
     * @return UserTransactionReportDto[]
     */
    public function getUserReport(UserReportDto $dto): array
    {
        $results = $this->databaseService->query("
            SELECT 
                user_id,
                SUM(
                    CASE
                        WHEN type = 'credit' THEN amount
                        WHEN type = 'deposit' THEN -amount
                    END
                ) as total_amount,
                DATE(created_at) AS transaction_day 
            FROM
                transactions
            WHERE
                user_id = ?
                AND (? IS NULL OR created_at >= ?)
                AND (? IS NULL OR created_at <= ?)
            GROUP BY 
                user_id,
                DATE(created_at)
            ORDER BY 
                DATE(created_at) ASC
        ", [
            $dto->userId,
            $dto->start,
            $dto->start,
            $dto->end,
            $dto->end
        ]);
        return array_map(
            fn ($row) => new UserTransactionReportDto(
                $row['user_id'],
                $row['total_amount'],
                Carbon::parse($row['transaction_day'])
            ),
            $results
        );
    }

    /**
     * @return UsersTransactionReportDto[]
     */
    public function getUsersReport(): array
    {
        $results = $this->databaseService->query("
            SELECT 
                SUM(
                    CASE
                        WHEN type = 'credit' THEN amount
                        WHEN type = 'deposit' THEN -amount
                    END
                ) as total_amount,
                DATE(created_at) AS transaction_day 
            FROM
                transactions
            GROUP BY 
                DATE(created_at)
            ORDER BY 
                DATE(created_at) ASC
        ");
        return array_map(
            fn ($row) => new UsersTransactionReportDto(
                $row['total_amount'],
                Carbon::parse($row['transaction_day'])
            ),
            $results
        );
    }
}