<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\TransactionService;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $transactionService = app(TransactionService::class);

        // Ana
        $anaUser = User::create([
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => Hash::make('password'),
        ]);

        $ana = Client::create([
            'user_id' => $anaUser->id,
            'name' => 'Ana',
        ]);

        $anaAccount = $ana->account()->create([
            'currency' => 'EUR',
            'cash_balance' => '0.00',
            'holdings_balance' => '0.00',
            'total_balance' => '0.00',
        ]);

        $transactionService->deposit($anaAccount, '1000.00');
        $transactionService->buy($anaAccount, 'AAPL', 5, '100.00');
        $transactionService->sell($anaAccount, 'AAPL', 3, '120.00');


        // Mark
        $markUser = User::create([
            'name' => 'Mark',
            'email' => 'mark@example.com',
            'password' => Hash::make('password'),
        ]);

        $mark = Client::create([
            'user_id' => $markUser->id,
            'name' => 'Mark',
        ]);

        $markAccount = $mark->account()->create([
            'currency' => 'USD',
            'cash_balance' => '0.00',
            'holdings_balance' => '0.00',
            'total_balance' => '0.00',
        ]);

        $transactionService->deposit($markAccount, '2500.00');
        $transactionService->buy($markAccount, 'MSFT', 4, '250.00');
    }
}
