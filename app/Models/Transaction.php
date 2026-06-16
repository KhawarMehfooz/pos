<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes, BelongsToUser;
    
    protected $fillable = [
        'customer_id',
        'status',
        'payment_method',
        'subtotal',
        'discount',
        'tax_amount',
        'grand_total',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function items(){
        return $this->hasMany(TransactionItem::class);
    }

    public function calculateTotals(): void
    {
        $setting  = Setting::current();
        $subtotal = $this->items()->sum('total');
        $preTax   = $subtotal - $this->discount;

        $tax = 0;
        if ($setting->gst_enabled) {
            $tax += round($preTax * $setting->gst_percentage / 100, 2);
        }
        if ($setting->vat_enabled) {
            $tax += round($preTax * $setting->vat_percentage / 100, 2);
        }

        $this->subtotal    = $subtotal;
        $this->tax_amount  = $tax;
        $this->grand_total = $preTax + $tax;
        $this->save();
    }
}
