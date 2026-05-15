<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;

class GoodsReceiptController extends Controller
{
    /**
     * Display a listing of the goods receipts.
     */
    public function index()
    {
        $stats = [
            'in_progress' => \App\Models\PurchaseRequisition::whereIn('status', ['Approved', 'Partially Approved'])->count(),
            'received' => \App\Models\GoodsReceipt::count(),
            'total_items' => \DB::table('goods_receipt_items')->sum('received_quantity')
        ];

        // Format total items
        $items = $stats['total_items'];
        if ($items >= 1000) {
            $stats['total_items_formatted'] = round($items / 1000, 1) . 'k';
        } else {
            $stats['total_items_formatted'] = number_format($items, 0, ',', '.');
        }

        return view('pages.inventory.goods-receipt.index', compact('stats'));
    }

    /**
     * Show the form for creating a new goods receipt.
     */
    public function create()
    {
        $units = Unit::orderBy('name', 'asc')->get();
        return view('pages.inventory.goods-receipt.create', compact('units'));
    }

    /**
     * Store a newly created goods receipt in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_photo' => 'nullable|image|max:2048',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0.01',
            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0',
        ]);

        return \DB::transaction(function () use ($request) {
            // Generate Identifier: GR-YYYYMMDD-NNN
            $date = date('Ymd', strtotime($request->date));
            $count = \App\Models\GoodsReceipt::whereDate('received_date', $request->date)->count() + 1;
            $identifier = 'GR-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            $invoicePath = null;
            if ($request->hasFile('invoice_photo')) {
                $invoicePath = $request->file('invoice_photo')->store('invoices', 'public');
            } elseif ($request->companion_photo_url) {
                // Photo from companion camera
                $companionSession = \App\Models\CompanionSession::where('user_id', auth()->id())
                    ->whereNotNull('photo_path')
                    ->latest('photo_uploaded_at')
                    ->first();
                
                if ($companionSession && $companionSession->photo_path) {
                    $sourcePath = storage_path('app/public/' . $companionSession->photo_path);
                    if (file_exists($sourcePath)) {
                        $newFilename = 'invoices/' . basename($companionSession->photo_path);
                        $destPath = storage_path('app/public/' . $newFilename);
                        if (!is_dir(dirname($destPath))) mkdir(dirname($destPath), 0755, true);
                        rename($sourcePath, $destPath);
                        $invoicePath = $newFilename;
                    }
                }
            }

            $goodsReceipt = \App\Models\GoodsReceipt::create([
                'identifier' => $identifier,
                'received_date' => $request->date,
                'supplier_id' => $request->supplier_id,
                'invoice_photo_path' => $invoicePath,
                'purchase_requisition_id' => $request->purchase_requisition_id,
            ]);

            $salesByOrder = [];
            $internalCustomer = \App\Models\Customer::where('type', 'internal')->first() 
                ?? \App\Models\Customer::create(['name' => 'Internal ERP Customer', 'type' => 'internal']);

            foreach ($request->quantity as $index => $qty) {
                $productId = $request->product_id[$index];
                $price = $request->price[$index];
                $notes = $request->notes[$index] ?? null;
                $orderRef = $request->order_reference[$index] ?? null;

                $item = $goodsReceipt->items()->create([
                    'product_id' => $productId,
                    'received_quantity' => $qty,
                    'unit' => $request->unit[$index] ?? ($product ? $product->base_unit : 'Pcs'), 
                    'unit_price' => $price,
                    'order_reference' => $orderRef,
                    'notes' => $notes,
                ]);

                // Update GR main order reference if not set
                if ($orderRef && !$goodsReceipt->order_reference) {
                    $goodsReceipt->update(['order_reference' => $orderRef]);
                }

                // Create Lot for FIFO
                $lot = \App\Models\Lot::create([
                    'identifier' => $identifier . '-' . ($index + 1),
                    'product_id' => $productId,
                    'goods_receipt_item_id' => $item->id,
                    'initial_quantity' => $qty,
                    'remaining_quantity' => $qty,
                    'unit_cost' => $price,
                ]);

                // If linked to an order, create automated sale
                if ($orderRef) {
                    if (!isset($salesByOrder[$orderRef])) {
                        $datePrefix = date('Ymd');
                        $lastSale = \App\Models\Sale::withTrashed()->where('invoice_number', 'like', "INV-{$datePrefix}-%")->orderBy('invoice_number', 'desc')->first();
                        $nextNumber = $lastSale ? ((int) substr($lastSale->invoice_number, -4)) + 1 : 1;
                        $saleInvoice = "INV-{$datePrefix}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                        $salesByOrder[$orderRef] = \App\Models\Sale::create([
                            'invoice_number' => $saleInvoice,
                            'customer_id' => $internalCustomer->id,
                            'order_reference' => $orderRef, // Store ERP Order Number
                            'sale_date' => now(),
                            'status' => 'Paid',
                            'notes' => 'Otomatis dari GR ' . $identifier . ' untuk Order: ' . $orderRef,
                            'created_by' => auth()->id(),
                        ]);
                    }

                    $sale = $salesByOrder[$orderRef];
                    
                    \App\Models\SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $productId,
                        'lot_id' => $lot->id,
                        'order_reference' => $orderRef,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $qty * $price,
                    ]);

                    // Update Sale total
                    $sale->increment('total_amount', $qty * $price);
                    
                    // Decrement Lot stock (immediately sold)
                    $lot->decrement('remaining_quantity', $qty);
                }
            }

            $response = [
                'status' => 'success',
                'message' => 'Goods Receipt ' . $identifier . ' berhasil disimpan!' . (count($salesByOrder) > 0 ? ' Serta otomatis membuat ' . count($salesByOrder) . ' Penjualan.' : ''),
                'redirect' => route('inventory.goods-receipt.index')
            ];

            // Trigger ERP Webhook for GR
            $goodsReceipt->load(['supplier', 'items.product', 'items.lots']);
            $webhookItems = [];
            foreach ($goodsReceipt->items as $item) {
                foreach ($item->lots as $lot) {
                    $webhookItems[] = [
                        'sku' => $item->product->sku,
                        'name' => $item->product->name,
                        'lot' => $lot->identifier,
                        'order_reference' => $item->order_reference,
                        'quantity' => $lot->initial_quantity,
                        'unit' => $item->unit,
                        'price' => $item->unit_price,
                        'total' => $lot->initial_quantity * $item->unit_price,
                    ];
                }
            }

            \App\Services\StockWebhookService::notify('create', 'purchase', [
                'goods_receipt_id' => $goodsReceipt->id,
                'identifier' => $goodsReceipt->identifier,
                'order_reference' => $goodsReceipt->order_reference, // ERP Order Number
                'supplier' => $goodsReceipt->supplier->name,
                'items' => $webhookItems
            ]);

            // Trigger ERP Webhook for each created automated Sale
            foreach ($salesByOrder as $orderRef => $sale) {
                $sale->load(['customer', 'items.product', 'items.lot']);
                \App\Services\StockWebhookService::notify('create', 'sale', [
                    'sale_id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'order_reference' => $sale->order_reference, // ERP Order Number
                    'customer' => [
                        'name' => $sale->customer->name,
                        'type' => $sale->customer->type,
                    ],
                    'items' => $sale->items->map(function($item) {
                        return [
                            'sku' => $item->product->sku,
                            'name' => $item->product->name,
                            'lot' => $item->lot->identifier ?? 'N/A',
                            'order_reference' => $item->order_reference,
                            'quantity' => $item->quantity,
                            'unit' => $item->product->base_unit,
                            'price' => $item->unit_price,
                            'subtotal' => $item->subtotal,
                        ];
                    })
                ]);
            }

            return response()->json($response);
        });
    }

    /**
     * Display the specified goods receipt.
     */
    public function show($id)
    {
        $receipt = \App\Models\GoodsReceipt::with(['items.product', 'supplier', 'purchaseRequisition'])->findOrFail($id);
        return view('pages.inventory.goods-receipt.show', compact('receipt'));
    }

    /**
     * Show the form for editing the specified goods receipt.
     */
    public function edit($id)
    {
        $receipt = \App\Models\GoodsReceipt::with(['items.product', 'supplier'])->findOrFail($id);
        $units = Unit::orderBy('name', 'asc')->get();
        return view('pages.inventory.goods-receipt.edit', compact('receipt', 'units'));
    }

    /**
     * Update the specified goods receipt in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_photo' => 'nullable|image|max:2048',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0.01',
            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0',
        ]);

        return \DB::transaction(function () use ($request, $id) {
            $goodsReceipt = \App\Models\GoodsReceipt::findOrFail($id);
            
            $invoicePath = $goodsReceipt->invoice_photo_path;
            if ($request->hasFile('invoice_photo')) {
                // Delete old photo if exists
                if ($invoicePath && \Storage::disk('public')->exists($invoicePath)) {
                    \Storage::disk('public')->delete($invoicePath);
                }
                $invoicePath = $request->file('invoice_photo')->store('invoices', 'public');
            } elseif ($request->companion_photo_url) {
                // Photo from companion camera
                $companionSession = \App\Models\CompanionSession::where('user_id', auth()->id())
                    ->whereNotNull('photo_path')
                    ->latest('photo_uploaded_at')
                    ->first();
                
                if ($companionSession && $companionSession->photo_path) {
                    // Delete old photo if exists
                    if ($invoicePath && \Storage::disk('public')->exists($invoicePath)) {
                        \Storage::disk('public')->delete($invoicePath);
                    }

                    $sourcePath = storage_path('app/public/' . $companionSession->photo_path);
                    if (file_exists($sourcePath)) {
                        $newFilename = 'invoices/' . basename($companionSession->photo_path);
                        $destPath = storage_path('app/public/' . $newFilename);
                        if (!is_dir(dirname($destPath))) mkdir(dirname($destPath), 0755, true);
                        rename($sourcePath, $destPath);
                        $invoicePath = $newFilename;
                    }
                }
            }

            $goodsReceipt->update([
                'received_date' => $request->date,
                'supplier_id' => $request->supplier_id,
                'invoice_photo_path' => $invoicePath,
            ]);

            // Update items
            // Note: This is a simplified version. For a production system, 
            // we should handle adding/removing items and updating Lots/Sales.
            // For now, we update existing items matching by index if possible,
            // or just update based on the request.
            
            foreach ($request->quantity as $index => $qty) {
                $productId = $request->product_id[$index];
                $price = $request->price[$index];
                $notes = $request->notes[$index] ?? null;
                $orderRef = $request->order_reference[$index] ?? null;
                $unit = $request->unit[$index] ?? 'Pcs';

                // Find existing item by index or create new
                $item = $goodsReceipt->items()->skip($index)->first();
                
                if ($item) {
                    $item->update([
                        'product_id' => $productId,
                        'received_quantity' => $qty,
                        'unit' => $unit,
                        'unit_price' => $price,
                        'order_reference' => $orderRef,
                        'notes' => $notes,
                    ]);

                    // Update Lot if it exists
                    $lot = \App\Models\Lot::where('goods_receipt_item_id', $item->id)->first();
                    if ($lot) {
                        $lot->update([
                            'product_id' => $productId,
                            'initial_quantity' => $qty,
                            'remaining_quantity' => $qty - ($lot->initial_quantity - $lot->remaining_quantity),
                            'unit_cost' => $price,
                        ]);
                    }
                } else {
                    // New item added during edit
                    $item = $goodsReceipt->items()->create([
                        'product_id' => $productId,
                        'received_quantity' => $qty,
                        'unit' => $unit,
                        'unit_price' => $price,
                        'order_reference' => $orderRef,
                        'notes' => $notes,
                    ]);

                    // Create Lot
                    \App\Models\Lot::create([
                        'identifier' => $goodsReceipt->identifier . '-' . ($index + 1),
                        'product_id' => $productId,
                        'goods_receipt_item_id' => $item->id,
                        'initial_quantity' => $qty,
                        'remaining_quantity' => $qty,
                        'unit_cost' => $price,
                    ]);
                }
            }

            // Remove items that are no longer in the request
            if ($goodsReceipt->items()->count() > count($request->quantity)) {
                $itemsToDrop = $goodsReceipt->items()->skip(count($request->quantity))->get();
                foreach ($itemsToDrop as $item) {
                    \App\Models\Lot::where('goods_receipt_item_id', $item->id)->delete();
                    $item->delete();
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Goods Receipt ' . $goodsReceipt->identifier . ' berhasil diperbarui!',
                'redirect' => route('inventory.goods-receipt.index')
            ]);
        });
    }
}
