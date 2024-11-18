<?php

namespace App\DTOs\User;

class CreateUserDto
{
    public readonly string $name;
    public readonly int $credit;

    /**
     * @param string $name
     * @param int $credit
     */
    public function __construct(string $name, int $credit)
    {
        $this->name = $name;
        $this->credit = $credit;
    }


}