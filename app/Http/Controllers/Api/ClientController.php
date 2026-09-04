<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Client::with('account')->get()
        );
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $data = $request->validated();

        $client = DB::transaction(function () use ($data) {
            $client = Client::create([
                'name' => $data['name'],
            ]);

            $client->account()->create([
                'currency' => strtoupper($data['currency']),
                'cash_balance' => '0.00',
                'holdings_balance' => '0.00',
                'total_balance' => '0.00',
            ]);

            return $client;
        });

        return response()->json(
            $client->load('account'),
            201
        );
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json(
            $client->load('account.holdings')
        );
    }
}