<?php

namespace App\Models;

class User
{
    public readonly int $id;
    public readonly string $name;
    public readonly int $credit;

    /**
     * @param int $id
     * @param string $name
     * @param int $credit
     */
    public function __construct(int $id, string $name, int $credit)
    {
        $this->id = $id;
        $this->name = $name;
        $this->credit = $credit;
    }


}
