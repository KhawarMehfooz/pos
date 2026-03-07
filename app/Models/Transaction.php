<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'customer_id',
        'status',
        'subtotal',
        'discount',
        'grand_total'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function items(){
        return $this->hasMany(TransactionItem::class);
    }

    public function calculateTotals(){
        $subtotal = $this->items()->sum('total');
        $this->subtotal = $subtotal;
        $this->grand_total = $subtotal - $this->discount;
        $this->save();
    }
}
