<?php

namespace App\Enums;

enum TransactionType: string
{
    case CREDIT = 'credit';
    case DEPOSIT = 'deposit';
}
