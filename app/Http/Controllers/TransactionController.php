<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function held(): JsonResponse
    {
        $transactions = Transaction::where('status', 'hold')
            ->with(['items.product', 'customer'])
            ->latest()
            ->get();

        return response()->json(['transactions' => $transactions]);
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        abort_if($transaction->status !== 'hold', 403, 'Only held transactions can be deleted.');
        $transaction->forceDelete();

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'customer_id'    => 'nullable|exists:customers,id',
            'discount'       => 'nullable|numeric|min:0',
            'status'         => 'required|in:hold,completed',
            'payment_method' => 'required|in:cash,card',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'paid_amount'    => 'required|numeric|min:0',
        ]);

        // Create the transaction using the service
        $transaction = TransactionService::createTransaction(
            $request->customer_id,
            $request->items,
            $request->discount ?? 0,
            $request->status,
            $request->paid_amount ?? 0,
            $request->payment_method
        );

        $transaction->load(['items', 'customer']);

        $setting = Setting::current();
        $receiptHtml = view('receipt', compact('transaction', 'setting'))->render();

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction->id,
            'message' => 'Transaction created successfully',
            'receipt_html' => $receiptHtml,
        ]);
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load(['items', 'customer']);

        $setting = Setting::current();

        return view('receipt', compact('transaction', 'setting'));
    }
}
