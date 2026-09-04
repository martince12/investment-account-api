<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionValidationTest extends TestCase
{
    use RefreshDatabase;

    private function createAccount(): Account
    {
        $user = User::factory()->create([
            'name' => 'Ana',
        ]);

        $client = $user->client()->create([
            'name' => 'Ana',
        ]);

        return $client->account()->create([
            'currency' => 'EUR',
            'cash_balance' => '1000.00',
            'holdings_balance' => '0.00',
            'total_balance' => '1000.00',
        ]);
    }

    public function test_transaction_type_must_be_valid(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson(
            "/api/accounts/{$account->id}/transactions",
            [
                'type' => 'invalid',
                'amount' => '100.00',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    public function test_deposit_amount_must_be_greater_than_zero(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson(
            "/api/accounts/{$account->id}/transactions",
            [
                'type' => 'deposit',
                'amount' => '0.00',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_negative_amount_is_rejected(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson(
            "/api/accounts/{$account->id}/transactions",
            [
                'type' => 'withdrawal',
                'amount' => '-50.00',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    public function test_buy_requires_ticker_quantity_and_price(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson(
            "/api/accounts/{$account->id}/transactions",
            [
                'type' => 'buy',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'ticker',
                'quantity',
                'price',
            ]);
    }

    public function test_quantity_must_be_positive_integer(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson(
            "/api/accounts/{$account->id}/transactions",
            [
                'type' => 'buy',
                'ticker' => 'AAPL',
                'quantity' => 1.5,
                'price' => '100.00',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_zero_quantity_is_rejected(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson(
            "/api/accounts/{$account->id}/transactions",
            [
                'type' => 'buy',
                'ticker' => 'AAPL',
                'quantity' => 0,
                'price' => '100.00',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_price_must_be_greater_than_zero(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson(
            "/api/accounts/{$account->id}/transactions",
            [
                'type' => 'buy',
                'ticker' => 'AAPL',
                'quantity' => 5,
                'price' => '0.00',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_cash_transaction_cannot_contain_security_fields(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson(
            "/api/accounts/{$account->id}/transactions",
            [
                'type' => 'deposit',
                'amount' => '100.00',
                'ticker' => 'AAPL',
                'quantity' => 1,
                'price' => '100.00',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'ticker',
                'quantity',
                'price',
            ]);
    }

    public function test_buy_cannot_accept_client_provided_amount(): void
    {
        $account = $this->createAccount();

        $response = $this->postJson(
            "/api/accounts/{$account->id}/transactions",
            [
                'type' => 'buy',
                'ticker' => 'AAPL',
                'quantity' => 5,
                'price' => '100.00',
                'amount' => '1.00',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }
}