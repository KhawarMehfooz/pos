<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "product_name",
        "category_id",
        "purchase_price",
        "retail_price",
        "sale_price",
        "product_image",
        "barcode",
        "sku",
        "track_stock",
        "stock_quantity",
        "min_stock_level",
        "is_active"
    ];

    protected function casts()
    {
        return [
            "track_stock" => "boolean",
            "is_active" => "boolean",
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // this will only return those products whose are active and category is also active
    public function scopeAvailableForSale($query)
    {
        return $query->where('is_active', true)
            ->whereHas('category', function ($q) {
                $q->where('is_active', true);
            });
    }
}
