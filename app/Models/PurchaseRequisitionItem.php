<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_requisition_id',
        'product_id',
        'supplier_id',
        'requested_quantity',
        'unit',
        'estimated_unit_price',
        'context',
        'erp_order_reference',
        'status',
        'rejection_reason',
        'notes'
    ];

    protected $casts = [
        'requested_quantity' => 'decimal:2',
        'estimated_unit_price' => 'decimal:2'
    ];

    /**
     * Get the purchase requisition that this item belongs to.
     */
    public function purchaseRequisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class);
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
