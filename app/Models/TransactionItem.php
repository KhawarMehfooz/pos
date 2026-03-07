<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name',
        'product_price',
        'quantity',
        'total'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function createItem(int $transactionId, $product, int $quantity = 1, array $options = []): self
    {
        $quantity = max(1, $quantity); 

        return self::create([
            'transaction_id' => $transactionId,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_price' => $product->price,
            'quantity' => $quantity,
            'total' => $product->price * $quantity,
            'metadata' => $options ? json_encode($options) : null, 
        ]);
    }
}
