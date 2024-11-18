<?php

namespace App\DTOs\Transaction;

use Carbon\Carbon;

class UserTransactionReportDto
{
    public readonly int $userId;
    public readonly int $totalAmount;
    public readonly Carbon $date;

    /**
     * @param int $userId
     * @param int $totalAmount
     * @param Carbon $date
     */
    public function __construct(int $userId, int $totalAmount, Carbon $date)
    {
        $this->userId = $userId;
        $this->totalAmount = $totalAmount;
        $this->date = $date;
    }


}