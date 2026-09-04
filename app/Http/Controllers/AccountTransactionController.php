<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Holding;
use App\Services\TransactionService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountTransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $account = Auth::user()
            ->client
            ->account;

        try {
            match ($data['type']) {
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

            return redirect()
                ->route('dashboard')
                ->with('success', 'Transaction completed successfully.');

        } catch (DomainException $exception) {
            return back()
                ->withErrors([
                    'transaction' => $exception->getMessage(),
                ])
                ->withInput();
        }
    }

    public function sell(Holding $holding): View
    {
        $account = Auth::user()
            ->client
            ->account;

        abort_unless(
            $holding->account_id === $account->id,
            403
        );

        return view('sell', [
            'holding' => $holding,
            'account' => $account,
        ]);
    }
}