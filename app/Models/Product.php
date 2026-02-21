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

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
