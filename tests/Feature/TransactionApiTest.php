<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionApiTest extends TestCase
{
    use RefreshDatabase;

    private function createAccount(string $cash = '100.00'): Account
    {
        $user = User::factory()->create([
            'name' => 'Ana',
        ]);

        $client = $user->client()->create([
            'name' => 'Ana',
        ]);

        return $client->account()->create([
            'currency' => 'EUR',
            'cash_balance' => $cash,
            'holdings_balance' => '0.00',
            'total_balance' => $cash,
        ]);
    }

    public function test_withdrawal_returns_clear_error_when_cash_is_insufficient(): void
    {
        $account = $this->createAccount('100.00');

        $response = $this->postJson(
            "/api/accounts/{$account->id}/transactions",
            [
                'type' => 'withdrawal',
                'amount' => '200.00',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Insufficient cash balance.',
            ]);

        $account->refresh();

        $this->assertSame('100.00', $account->cash_balance);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_sell_returns_clear_error_when_quantity_is_insufficient(): void
    {
        $account = $this->createAccount('500.00');

        $account->holdings()->create([
            'ticker' => 'AAPL',
            'quantity' => 5,
            'current_price' => '100.00',
            'current_value' => '500.00',
        ]);

        $account->update([
            'holdings_balance' => '500.00',
            'total_balance' => '1000.00',
        ]);

        $response = $this->postJson(
            "/api/accounts/{$account->id}/transactions",
            [
                'type' => 'sell',
                'ticker' => 'AAPL',
                'quantity' => 8,
                'price' => '120.00',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Insufficient holdings quantity.',
            ]);

        $account->refresh();

        $this->assertSame('500.00', $account->cash_balance);
        $this->assertSame('500.00', $account->holdings_balance);

        $this->assertDatabaseHas('holdings', [
            'account_id' => $account->id,
            'ticker' => 'AAPL',
            'quantity' => 5,
        ]);

        $this->assertDatabaseCount('transactions', 0);
    }
}