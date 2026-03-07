<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransactionService
{
    public static function createTransaction(
        ?int $customerId,
        array $cartItems,
        float $discount = 0,
        string $status = 'hold',
        float $paidAmount = 0
    ): Transaction {
        return DB::transaction(function () use ($customerId, $cartItems, $discount, $status, $paidAmount) {

            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new ModelNotFoundException("Product ID {$item['product_id']} not found.");
                }

                if ($product->track_stock && $product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product {$product->product_name}. Requested: {$item['quantity']}, Available: {$product->stock_quantity}");
                }
            }

            $transaction = Transaction::create([
                'customer_id' => $customerId,
                'status' => $status,
                'discount' => $discount,
            ]);

            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']); 

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_name' => $product->product_name,
                    'product_price' => $item['product_price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['product_price'] * $item['quantity'],
                    'sku' =>  $product->sku,
                    'barcode' =>  $product->barcode,
                ]);

                if ($product->track_stock) {
                    $product->stock_quantity = max($product->stock_quantity - $item['quantity'], 0);
                    $product->save();
                }
            }

            $transaction->calculateTotals();

            if ($status === 'completed') {
                $transaction->paid_amount = $paidAmount;
                $transaction->change_amount = max($paidAmount - $transaction->grand_total, 0);
                $transaction->save();
            }

            return $transaction;
        });
    }

    public static function completeTransaction(Transaction $transaction): Transaction
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->status = 'completed';
            $transaction->save();
            return $transaction;
        });
    }

    public static function holdTransaction(Transaction $transaction): Transaction
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->status = 'hold';
            $transaction->save();
            return $transaction;
        });
    }
}
