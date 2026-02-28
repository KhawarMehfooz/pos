<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes, HasFactory;

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

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
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

    protected $appends = [
        'product_image_url'
    ];

    public function getProductImageUrlAttribute(){
        if(!$this->product_image){
            return asset('images/product-placeholder.jpeg');
        }
        return Storage::disk('public')->url($this->product_image);
    }
}
