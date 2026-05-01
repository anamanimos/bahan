<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_receipt_id',
        'product_id',
        'received_quantity',
        'unit',
        'unit_price',
        'lot_identifier',
        'notes'
    ];

    protected $casts = [
        'received_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2'
    ];

    /**
     * Get the goods receipt that this item belongs to.
     */
    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
