<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use BelongsToUser;
    protected $fillable = [
        'store_name',
        'store_address',
        'store_phone',
        'currency_symbol',
        'gst_enabled',
        'gst_percentage',
        'vat_enabled',
        'vat_percentage',
    ];

    protected $casts = [
        'gst_enabled' => 'boolean',
        'vat_enabled' => 'boolean',
        'gst_percentage' => 'decimal:2',
        'vat_percentage' => 'decimal:2',
    ];

    /**
     * Always return the single settings row, creating it if it doesn't exist.
     */
    public static function current(): static
    {
        return static::firstOrCreate([], [
            'store_name' => 'My Store',
            'currency_symbol' => '$',
            'gst_enabled' => false,
            'gst_percentage' => 0,
            'vat_enabled' => false,
            'vat_percentage' => 0,
        ]);
    }
}
