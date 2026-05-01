<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone_number', 'contact_person'];

    /**
     * Get the purchase requisitions for this supplier.
     */
    public function purchaseRequisitionItems(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }

    /**
     * Get the goods receipts for this supplier.
     */
    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }
}
