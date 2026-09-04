<?php

namespace App\Http\Controllers\Api;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Account;
use App\Services\TransactionService;
use DomainException;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {
    }

    public function index(Account $account): JsonResponse
    {
        $transactions = $account->transactions()
            ->with('securityDetail')
            ->latest()
            ->get();

        return response()->json($transactions);
    }

    public function store(
        StoreTransactionRequest $request,
        Account $account
    ): JsonResponse {
        try {
            $data = $request->validated();

            $transaction = match ($data['type']) {
                TransactionType::Deposit->value =>
                    $this->transactionService->deposit(
                        $account,
                        $data['amount']
                    ),

                TransactionType::Withdrawal->value =>
                    $this->transactionService->withdraw(
                        $account,
                        $data['amount']
                    ),

                TransactionType::Buy->value =>
                    $this->transactionService->buy(
                        $account,
                        $data['ticker'],
                        $data['quantity'],
                        $data['price']
                    ),

                TransactionType::Sell->value =>
                    $this->transactionService->sell(
                        $account,
                        $data['ticker'],
                        $data['quantity'],
                        $data['price']
                    ),
            };

            return response()->json(
                $transaction->load('securityDetail'),
                201
            );
        } catch (DomainException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}