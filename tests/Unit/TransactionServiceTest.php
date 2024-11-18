<?php

namespace Tests\Unit;

use App\DTOs\Transaction\CreateTransactionDto;
use App\DTOs\Transaction\UserReportDto;
use App\DTOs\Transaction\UsersTransactionReportDto;
use App\DTOs\Transaction\UserTransactionReportDto;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use App\Services\CacheService;
use App\Services\TransactionService;
use Assert\AssertionFailedException;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class TransactionServiceTest extends TestCase
{
    private TransactionService $transactionService;
    private TransactionRepository $transactionRepository;
    private UserRepository $userRepository;
    private CacheService $cacheService;

    private Carbon $time;


    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionRepository = $this->createMock(TransactionRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->cacheService = $this->createMock(CacheService::class);
        $this->transactionService = new TransactionService(
            $this->transactionRepository,
            $this->userRepository,
            $this->cacheService
        );
        $this->time = Carbon::now();
        Carbon::setTestNow($this->time);
    }

    public function testCreateTransactionCredit()
    {
        $dto = new CreateTransactionDto(1, 100, TransactionType::CREDIT);
        $user = new User(1, 'aa', 500);
        $transaction = new Transaction(
            1, 100, TransactionType::CREDIT, 1, $this->time
        );

        $this->userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($user);
        $this->userRepository->expects($this->once())
            ->method('beginTransaction');
        $this->userRepository->expects($this->once())
            ->method('updateCredit')
            ->with(1, 100);
        $this->transactionRepository->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($transaction);
        $this->userRepository->expects($this->once())
            ->method('commit');

        $result = $this->transactionService->createTransaction($dto);

        $this->assertEquals($result, $transaction);
    }

    public function testCreateTransactionDepositWithSufficientCredit()
    {
        $dto = new CreateTransactionDto(1, 100, TransactionType::DEPOSIT);
        $user = new User(1, 'aa', 500);
        $transaction = new Transaction(
            1, 100, TransactionType::DEPOSIT, 1, $this->time
        );

        $this->userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($user);
        $this->userRepository->expects($this->once())
            ->method('beginTransaction');
        $this->userRepository->expects($this->once())
            ->method('getUserCreditForUpdate')
            ->with(1)
            ->willReturn(500);
        $this->userRepository->expects($this->once())
            ->method('updateCredit')
            ->with(1, -100);
        $this->transactionRepository->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($transaction);
        $this->userRepository->expects($this->once())
            ->method('commit');

        $result = $this->transactionService->createTransaction($dto);

        $this->assertEquals($result, $transaction);
    }

    public function testCreateTransactionDepositWithInsufficientCredit()
    {
        $this->expectException(AssertionFailedException::class);

        $dto = new CreateTransactionDto(1, 100, TransactionType::DEPOSIT);
        $user = new User(1, 'aa', 50);

        $this->userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($user);
        $this->userRepository->expects($this->once())
            ->method('beginTransaction');
        $this->userRepository->expects($this->once())
            ->method('getUserCreditForUpdate')
            ->with(1)
            ->willReturn(50);

        $this->transactionService->createTransaction($dto);
    }

    public function testCreateTransactionWithInvalidUser()
    {
        $this->expectException(AssertionFailedException::class);

        $dto = new CreateTransactionDto(999, 100, TransactionType::CREDIT);

        $this->userRepository->expects($this->once())
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        $this->transactionService->createTransaction($dto);
    }

    public function testCreateTransactionExceptionHandling()
    {
        $dto = new CreateTransactionDto( 1,  100, TransactionType::CREDIT);
        $user = new User(1, 'aa', 500);

        $this->userRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($user);
        $this->userRepository->expects($this->once())
            ->method('beginTransaction');
        $this->userRepository->expects($this->once())
            ->method('updateCredit')
            ->with(1, 100)
            ->willThrowException(new \Exception('Repository error'));
        $this->userRepository->expects($this->once())
            ->method('rollback');

        $this->expectException(\Exception::class);
        $this->transactionService->createTransaction($dto);
    }

    public function testReportOfUser()
    {
        $dto = new UserReportDto(1);
        $userTransactionReport = new UserTransactionReportDto(1, 100, $this->time);

        $this->transactionRepository->expects($this->once())
            ->method('getUserReport')
            ->with($dto)
            ->willReturn([$userTransactionReport]);

        $result = $this->transactionService->reportOfUser($dto);

        $this->assertIsArray($result);
        $this->assertContainsOnlyInstancesOf(UserTransactionReportDto::class, $result);
        $this->assertEquals($result[0], $userTransactionReport);
    }

    public function testReportOfUsers()
    {
        $usersTransactionReport = new UsersTransactionReportDto(12, $this->time);

        $this->cacheService->expects($this->once())
            ->method('get')
            ->with('report', $this->isType('callable'))
            ->willReturn([$usersTransactionReport]);

        $result = $this->transactionService->reportOfUsers();

        $this->assertIsArray($result);
        $this->assertContainsOnlyInstancesOf(UsersTransactionReportDto::class, $result);
        $this->assertEquals($result[0], $usersTransactionReport);
    }
}