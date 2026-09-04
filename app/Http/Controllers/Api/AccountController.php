<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function show(Account $account): JsonResponse
    {
        $account->load('client', 'holdings');

        return response()->json([
            'id' => $account->id,
            'client' => [
                'id' => $account->client->id,
                'name' => $account->client->name,
            ],
            'currency' => $account->currency,
            'total_balance' => $account->total_balance,
            'cash_balance' => $account->cash_balance,
            'holdings_balance' => $account->holdings_balance,
            'holdings' => $account->holdings->map(fn ($holding) => [
                'ticker' => $holding->ticker,
                'quantity' => $holding->quantity,
                'current_price' => $holding->current_price,
                'current_value' => $holding->current_value,
            ]),
        ]);
    }
}