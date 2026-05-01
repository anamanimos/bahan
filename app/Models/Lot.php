<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'product_id',
        'goods_receipt_item_id',
        'initial_quantity',
        'remaining_quantity',
        'unit_cost',
        'expiry_date'
    ];

    protected $casts = [
        'initial_quantity' => 'decimal:2',
        'remaining_quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'expiry_date' => 'date'
    ];

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the goods receipt item that created this lot.
     */
    public function goodsReceiptItem(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptItem::class);
    }
}
