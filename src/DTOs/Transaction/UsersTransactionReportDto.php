<?php

namespace App\DTOs\Transaction;

use Carbon\Carbon;

class UsersTransactionReportDto
{
    public readonly int $totalAmount;
    public readonly Carbon $date;

    /**
     * @param int $totalAmount
     * @param Carbon $date
     */
    public function __construct(int $totalAmount, Carbon $date)
    {
        $this->totalAmount = $totalAmount;
        $this->date = $date;
    }
}