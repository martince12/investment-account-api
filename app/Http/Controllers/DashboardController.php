<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $client = Auth::user()
            ->client()
            ->with('account.holdings')
            ->firstOrFail();

        return view('dashboard', [
            'client' => $client,
            'account' => $client->account,
        ]);
    }
}