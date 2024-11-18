<?php

namespace App\Services;

use App\DTOs\Transaction\CreateTransactionDto;
use App\DTOs\Transaction\UserReportDto;
use App\DTOs\Transaction\UsersTransactionReportDto;
use App\DTOs\Transaction\UserTransactionReportDto;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use Assert\Assertion;

class TransactionService
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private UserRepository $userRepository,
        private CacheService $cacheService,
    )
    {
    }

    public function createTransaction(CreateTransactionDto $dto): Transaction
    {
        $user = $this->userRepository->findById($dto->userId);
        Assertion::notNull($user, 'Invalid user');

        $this->userRepository->beginTransaction();
        try {
            if ($dto->type == TransactionType::CREDIT) {
                $this->userRepository->updateCredit($dto->userId, $dto->amount);
                $transaction = $this->transactionRepository->create($dto);
            } else {
                $credit = $this->userRepository->getUserCreditForUpdate($user->id);
                Assertion::greaterOrEqualThan($credit, $dto->amount, 'Not enough credit');
                $this->userRepository->updateCredit($dto->userId, $dto->amount * -1);
                $transaction = $this->transactionRepository->create($dto);
            }
            $this->userRepository->commit();
            return $transaction;
        } catch (\Throwable $th) {
            $this->userRepository->rollback();
            throw $th;
        }
    }

    /**
     * @param UserReportDto $dto
     * @return UserTransactionReportDto[]
     */
    public function reportOfUser(UserReportDto $dto): array
    {
        return $this->transactionRepository->getUserReport($dto);
    }

    /**
     * @return UsersTransactionReportDto[]
     */
    public function reportOfUsers(): array
    {
        return $this->cacheService->get('report', function () {
            return $this->transactionRepository->getUsersReport();
        });
    }
}