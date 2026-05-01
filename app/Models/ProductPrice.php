<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'price_type',
        'price',
        'minimum_quantity',
        'effective_date',
        'expiry_date'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'minimum_quantity' => 'decimal:2',
        'effective_date' => 'date',
        'expiry_date' => 'date'
    ];

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
