<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'required|in:hold,completed',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        // Create the transaction using the service
        $transaction = TransactionService::createTransaction(
            $request->customer_id,
            $request->items,
            $request->discount ?? 0,
            $request->status,
            $request->paid_amount ?? 0
        );

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction->id,
            'message' => 'Transaction created successfully',
        ]);
    }
}
