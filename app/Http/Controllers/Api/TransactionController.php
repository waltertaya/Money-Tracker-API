<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Wallet $wallet): JsonResponse
    {
        $transactions = $wallet->transactions()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'wallet' => [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'balance' => $wallet->balance,
            ],
            'transactions' => $transactions,
        ]);
    }

    public function store(Request $request, Wallet $wallet): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
        ]);

        $transaction = $wallet->transactions()->create($validated);

        return response()->json([
            'message' => 'Transaction created successfully.',
            'data' => $transaction,
            'wallet_balance' => $wallet->fresh()->balance,
        ], 201);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        return response()->json([
            'data' => $transaction,
        ]);
    }
}
