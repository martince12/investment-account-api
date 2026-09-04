<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Holding;
use App\Models\SecurityTransactionDetail;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function deposit(Account $account, string $amount): Transaction
    {
        return DB::transaction(function () use ($account, $amount) {

            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($account->id);

            $account->cash_balance = bcadd(
                $account->cash_balance,
                $amount,
                2
            );

            $account->total_balance = bcadd(
                $account->cash_balance,
                $account->holdings_balance,
                2
            );

            $account->save();

            return Transaction::create([
                'account_id' => $account->id,
                'type' => TransactionType::Deposit,
                'amount' => $amount,
            ]);
        });
    }

    public function withdraw(Account $account, string $amount): Transaction
    {
        return DB::transaction(function () use ($account, $amount) {

            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($account->id);

            if (bccomp($account->cash_balance, $amount, 2) < 0) {
                throw new \DomainException('Insufficient cash balance.');
            }

            $account->cash_balance = bcsub($account->cash_balance, $amount, 2);

            $account->total_balance = bcadd(
                $account->cash_balance,
                $account->holdings_balance,
                2
            );

            $account->save();

            return Transaction::create([
                'account_id' => $account->id,
                'type' => TransactionType::Withdrawal,
                'amount' => $amount,
            ]);
        });
    }

    public function buy(
    Account $account,
    string $ticker,
    int $quantity,
    string $price
    ): Transaction {
        return DB::transaction(function () use ($account, $ticker, $quantity, $price) {

            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($account->id);

            $cost = bcmul((string) $quantity, $price, 2);

            if (bccomp($account->cash_balance, $cost, 2) < 0) {
                throw new \DomainException('Insufficient cash balance.');
            }

            $holding = Holding::where('account_id', $account->id)
                ->where('ticker', $ticker)
                ->first();

            $oldHoldingValue = $holding?->current_value ?? '0.00';

            if ($holding) {
                $holding->quantity += $quantity;
                $holding->current_price = $price;
                $holding->current_value = bcmul(
                    (string) $holding->quantity,
                    $price,
                    2
                );

                $holding->save();
            } else {
                $holding = Holding::create([
                    'account_id' => $account->id,
                    'ticker' => $ticker,
                    'quantity' => $quantity,
                    'current_price' => $price,
                    'current_value' => $cost,
                ]);
            }

            $account->cash_balance = bcsub(
                $account->cash_balance,
                $cost,
                2
            );

            $account->holdings_balance = bcadd(
                bcsub(
                    $account->holdings_balance,
                    $oldHoldingValue,
                    2
                ),
                $holding->current_value,
                2
            );

            $account->total_balance = bcadd(
                $account->cash_balance,
                $account->holdings_balance,
                2
            );

            $account->save();

            $transaction = Transaction::create([
                'account_id' => $account->id,
                'type' => TransactionType::Buy,
                'amount' => $cost,
            ]);

            SecurityTransactionDetail::create([
                'transaction_id' => $transaction->id,
                'ticker' => $ticker,
                'quantity' => $quantity,
                'price' => $price,
            ]);

            return $transaction;
        });
    }

    public function sell(
    Account $account,
    string $ticker,
    int $quantity,
    string $price
    ): Transaction {
        return DB::transaction(function () use ($account, $ticker, $quantity, $price) {

            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($account->id);

            $holding = Holding::where('account_id', $account->id)
                ->where('ticker', $ticker)
                ->first();

            if (!$holding || $holding->quantity < $quantity) {
                throw new \DomainException('Insufficient holdings quantity.');
            }

            $proceeds = bcmul((string) $quantity, $price, 2);

            $oldHoldingValue = $holding->current_value;

            $remainingQuantity = $holding->quantity - $quantity;

            if ($remainingQuantity === 0) {
                $newHoldingValue = '0.00';
                $holding->delete();
            } else {
                $holding->quantity = $remainingQuantity;
                $holding->current_price = $price;
                $holding->current_value = bcmul(
                    (string) $remainingQuantity,
                    $price,
                    2
                );

                $holding->save();

                $newHoldingValue = $holding->current_value;
            }

            $account->cash_balance = bcadd(
                $account->cash_balance,
                $proceeds,
                2
            );

            $account->holdings_balance = bcadd(
                bcsub(
                    $account->holdings_balance,
                    $oldHoldingValue,
                    2
                ),
                $newHoldingValue,
                2
            );

            $account->total_balance = bcadd(
                $account->cash_balance,
                $account->holdings_balance,
                2
            );

            $account->save();

            $transaction = Transaction::create([
                'account_id' => $account->id,
                'type' => TransactionType::Sell,
                'amount' => $proceeds,
            ]);

            SecurityTransactionDetail::create([
                'transaction_id' => $transaction->id,
                'ticker' => $ticker,
                'quantity' => $quantity,
                'price' => $price,
            ]);

            return $transaction;
        });
    }

    
}