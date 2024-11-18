<?php

namespace Tests\Unit;

use App\DTOs\User\CreateUserDto;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private UserService $userService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userService = new UserService($this->userRepository);
    }

    public function testCreateUser()
    {
        
        $dto = new CreateUserDto('aa', 1200);
        $user = new User(1, 'aa', 1200);
        
        $this->userRepository->expects($this->once())
            ->method('createUser')
            ->with($dto) 
            ->willReturn($user); 

        
        $result = $this->userService->createUser($dto);

        
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($result, $user);
    }

    public function testCreateUserThrowsExceptionOnFailure()
    {
        
        $dto = new CreateUserDto('aa', 1200);
        
        $this->userRepository->expects($this->once())
            ->method('createUser')
            ->with($dto)
            ->willThrowException(new \Exception('Database error'));
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');

        $this->userService->createUser($dto);
    }
}