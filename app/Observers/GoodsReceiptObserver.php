<?php

namespace App\Observers;

use App\Models\GoodsReceipt;
use App\Services\MediaSyncService;

class GoodsReceiptObserver
{
    protected $syncService;

    public function __construct(MediaSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Handle the GoodsReceipt "saved" event.
     */
    public function saved(GoodsReceipt $goodsReceipt): void
    {
        if ($goodsReceipt->invoice_photo_path) {
            \App\Jobs\SyncMediaJob::dispatch($goodsReceipt->invoice_photo_path);
        }
    }
}
