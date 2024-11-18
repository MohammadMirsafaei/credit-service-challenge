<?php

namespace App\DTOs\Transaction;

use Carbon\Carbon;

class UserReportDto
{
    public readonly int $userId;
    public readonly ?Carbon $start;
    public readonly ?Carbon $end;

    /**
     * @param int $userId
     * @param Carbon|null $start
     * @param Carbon|null $end
     */
    public function __construct(int $userId, ?Carbon $start = null, ?Carbon $end = null)
    {
        $this->userId = $userId;
        $this->start = $start;
        $this->end = $end;
    }
}