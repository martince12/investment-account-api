<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionHistoryController extends Controller
{
    public function index(): View
    {
        $client = Auth::user()
            ->client()
            ->with([
                'account.transactions' => function ($query) {
                    $query->with('securityDetail')
                        ->latest();
                },
            ])
            ->firstOrFail();

        return view('history', [
            'client' => $client,
            'account' => $client->account,
            'transactions' => $client->account->transactions,
        ]);
    }
}