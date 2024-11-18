<?php

namespace App\DTOs\Transaction;

use App\Enums\TransactionType;

class CreateTransactionDto
{
    public readonly int $userId;
    public readonly int $amount;
    public readonly TransactionType $type;

    /**
     * @param int $userId
     * @param int $amount
     * @param TransactionType $type
     */
    public function __construct(int $userId, int $amount, TransactionType $type)
    {
        $this->userId = $userId;
        $this->amount = $amount;
        $this->type = $type;
    }


}