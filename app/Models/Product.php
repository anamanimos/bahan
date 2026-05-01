<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'category_id',
        'base_unit',
        'selling_unit',
        'conversion_factor',
        'specifications',
        'image_path',
        'warehouse_location',
        'minimum_stock_level',
        'is_active'
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:4',
        'minimum_stock_level' => 'decimal:2',
        'is_active' => 'boolean',
        'specifications' => 'array'
    ];

    /**
     * Get the category that the product belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the prices for the product.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Get the inventory lots for the product.
     */
    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    /**
     * Calculate current total stock across all lots.
     */
    public function getTotalStockAttribute(): float
    {
        return (float) $this->lots()->sum('remaining_quantity');
    }
}
