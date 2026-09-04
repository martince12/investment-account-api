<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_deposit_updates_cash_and_total_balance(): void
    {
        $client = Client::create([
            'name' => 'Ana',
        ]);

        $account = $client->account()->create([
            'currency' => 'EUR',
            'cash_balance' => '0.00',
            'holdings_balance' => '0.00',
            'total_balance' => '0.00',
        ]);

        $service = app(TransactionService::class);

        $transaction = $service->deposit($account, '1000.00');

        $account->refresh();

        $this->assertSame('1000.00', $account->cash_balance);
        $this->assertSame('0.00', $account->holdings_balance);
        $this->assertSame('1000.00', $account->total_balance);

        $this->assertSame('1000.00', $transaction->amount);
        $this->assertSame('deposit', $transaction->type->value);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 1000.00,
        ]);
    }

    public function test_withdrawal_updates_cash_and_total_balance(): void
    {
        $client = Client::create([
            'name' => 'Ana',
        ]);

        $account = $client->account()->create([
            'currency' => 'EUR',
            'cash_balance' => '1000.00',
            'holdings_balance' => '0.00',
            'total_balance' => '1000.00',
        ]);

        $service = app(TransactionService::class);

        $transaction = $service->withdraw($account, '300.00');

        $account->refresh();

        $this->assertSame('700.00', $account->cash_balance);
        $this->assertSame('700.00', $account->total_balance);
        $this->assertSame('300.00', $transaction->amount);
        $this->assertSame('withdrawal', $transaction->type->value);
    }

    public function test_withdrawal_fails_when_cash_is_insufficient(): void
    {
        $client = Client::create([
            'name' => 'Ana',
        ]);

        $account = $client->account()->create([
            'currency' => 'EUR',
            'cash_balance' => '200.00',
            'holdings_balance' => '0.00',
            'total_balance' => '200.00',
        ]);

        $service = app(TransactionService::class);

        try {
            $service->withdraw($account, '300.00');

            $this->fail('Expected DomainException was not thrown.');
        } catch (\DomainException $exception) {
            $this->assertSame(
                'Insufficient cash balance.',
                $exception->getMessage()
            );
        }

        $account->refresh();

        $this->assertSame('200.00', $account->cash_balance);
        $this->assertSame('200.00', $account->total_balance);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_buy_updates_balances_and_holding(): void
    {
        $client = Client::create(['name' => 'Ana']);

        $account = $client->account()->create([
            'currency' => 'EUR',
            'cash_balance' => '1000.00',
            'holdings_balance' => '0.00',
            'total_balance' => '1000.00',
        ]);

        $service = app(TransactionService::class);

        $transaction = $service->buy(
            $account,
            'AAPL',
            5,
            '100.00'
        );

        $account->refresh();

        $this->assertSame('500.00', $account->cash_balance);
        $this->assertSame('500.00', $account->holdings_balance);
        $this->assertSame('1000.00', $account->total_balance);

        $this->assertDatabaseHas('holdings', [
            'account_id' => $account->id,
            'ticker' => 'AAPL',
            'quantity' => 5,
            'current_price' => 100.00,
            'current_value' => 500.00,
        ]);

        $this->assertSame('buy', $transaction->type->value);
        $this->assertSame('500.00', $transaction->amount);
    }

    public function test_buy_fails_when_cash_is_insufficient(): void
    {
        $client = Client::create(['name' => 'Ana']);

        $account = $client->account()->create([
            'currency' => 'EUR',
            'cash_balance' => '200.00',
            'holdings_balance' => '0.00',
            'total_balance' => '200.00',
        ]);

        $service = app(TransactionService::class);

        try {
            $service->buy($account, 'AAPL', 5, '100.00');

            $this->fail('Expected DomainException was not thrown.');
        } catch (\DomainException $exception) {
            $this->assertSame(
                'Insufficient cash balance.',
                $exception->getMessage()
            );
        }

        $account->refresh();

        $this->assertSame('200.00', $account->cash_balance);
        $this->assertSame('0.00', $account->holdings_balance);
        $this->assertSame('200.00', $account->total_balance);

        $this->assertDatabaseCount('holdings', 0);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_sell_updates_balances_and_holding(): void
    {
        $client = Client::create(['name' => 'Ana']);

        $account = $client->account()->create([
            'currency' => 'EUR',
            'cash_balance' => '500.00',
            'holdings_balance' => '500.00',
            'total_balance' => '1000.00',
        ]);

        $account->holdings()->create([
            'ticker' => 'AAPL',
            'quantity' => 5,
            'current_price' => '100.00',
            'current_value' => '500.00',
        ]);

        $service = app(TransactionService::class);

        $transaction = $service->sell(
            $account,
            'AAPL',
            3,
            '120.00'
        );

        $account->refresh();

        $this->assertSame('860.00', $account->cash_balance);
        $this->assertSame('240.00', $account->holdings_balance);
        $this->assertSame('1100.00', $account->total_balance);

        $this->assertDatabaseHas('holdings', [
            'account_id' => $account->id,
            'ticker' => 'AAPL',
            'quantity' => 2,
            'current_price' => 120.00,
            'current_value' => 240.00,
        ]);

        $this->assertSame('sell', $transaction->type->value);
        $this->assertSame('360.00', $transaction->amount);
    }

    public function test_sell_fails_when_quantity_is_insufficient(): void
    {
        $client = Client::create(['name' => 'Ana']);

        $account = $client->account()->create([
            'currency' => 'EUR',
            'cash_balance' => '500.00',
            'holdings_balance' => '500.00',
            'total_balance' => '1000.00',
        ]);

        $account->holdings()->create([
            'ticker' => 'AAPL',
            'quantity' => 5,
            'current_price' => '100.00',
            'current_value' => '500.00',
        ]);

        $service = app(TransactionService::class);

        try {
            $service->sell($account, 'AAPL', 8, '120.00');

            $this->fail('Expected DomainException was not thrown.');
        } catch (\DomainException $exception) {
            $this->assertSame(
                'Insufficient holdings quantity.',
                $exception->getMessage()
            );
        }

        $account->refresh();

        $this->assertSame('500.00', $account->cash_balance);
        $this->assertSame('500.00', $account->holdings_balance);
        $this->assertSame('1000.00', $account->total_balance);

        $this->assertDatabaseHas('holdings', [
            'account_id' => $account->id,
            'ticker' => 'AAPL',
            'quantity' => 5,
        ]);

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_full_sell_removes_holding_but_keeps_transaction_history(): void
    {
        $client = Client::create(['name' => 'Ana']);

        $account = $client->account()->create([
            'currency' => 'EUR',
            'cash_balance' => '500.00',
            'holdings_balance' => '500.00',
            'total_balance' => '1000.00',
        ]);

        $account->holdings()->create([
            'ticker' => 'AAPL',
            'quantity' => 5,
            'current_price' => '100.00',
            'current_value' => '500.00',
        ]);

        $service = app(TransactionService::class);

        $service->sell($account, 'AAPL', 5, '120.00');

        $account->refresh();

        $this->assertSame('1100.00', $account->cash_balance);
        $this->assertSame('0.00', $account->holdings_balance);
        $this->assertSame('1100.00', $account->total_balance);

        $this->assertDatabaseMissing('holdings', [
            'account_id' => $account->id,
            'ticker' => 'AAPL',
        ]);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'sell',
            'amount' => 600.00,
        ]);
    }
}