<?php

namespace App\Services;

use App\DTOs\User\CreateUserDto;
use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    public function createUser(CreateUserDto $dto): User
    {
        return $this->userRepository->createUser($dto);
    }
}