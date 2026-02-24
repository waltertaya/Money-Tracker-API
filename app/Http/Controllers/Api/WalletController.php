<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $wallet = Wallet::create($validated);

        return response()->json([
            'message' => 'Wallet created successfully.',
            'data' => [
                'id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'name' => $wallet->name,
                'balance' => $wallet->balance,
                'created_at' => $wallet->created_at,
            ],
        ], 201);
    }

    public function show(Wallet $wallet): JsonResponse
    {
        $wallet->load(['transactions' => function ($query) {
            $query->orderByDesc('date')->orderByDesc('created_at');
        }]);

        return response()->json([
            'wallet' => [
                'id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'name' => $wallet->name,
                'balance' => $wallet->balance,
                'created_at' => $wallet->created_at,
            ],
            'transactions' => $wallet->transactions,
        ]);
    }

    public function update(Request $request, Wallet $wallet): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $wallet->update($validated);

        return response()->json([
            'message' => 'Wallet updated successfully.',
            'data' => [
                'id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'name' => $wallet->name,
                'balance' => $wallet->balance,
                'updated_at' => $wallet->updated_at,
            ],
        ]);
    }

    public function destroy(Wallet $wallet): JsonResponse
    {
        $wallet->delete();

        return response()->json([
            'message' => 'Wallet deleted successfully.',
        ]);
    }
}
