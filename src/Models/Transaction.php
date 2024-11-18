<?php

namespace App\Models;

use App\Enums\TransactionType;
use Carbon\Carbon;

class Transaction
{
    public readonly int $id;
    public readonly int $amount;
    public readonly TransactionType $type;
    public readonly int $userId;
    public readonly Carbon $createdAt;

    /**
     * @param int $id
     * @param int $amount
     * @param TransactionType $type
     * @param int $userId
     * @param Carbon $createdAt
     */
    public function __construct(int $id, int $amount, TransactionType $type, int $userId, Carbon $createdAt)
    {
        $this->id = $id;
        $this->amount = $amount;
        $this->type = $type;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
    }


}