<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return view('pages.inventory.goods-receipt.create');
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
                    'unit' => 'Mtr', 
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
                        $lastSale = \App\Models\Sale::where('invoice_number', 'like', "INV-{$datePrefix}-%")->orderBy('invoice_number', 'desc')->first();
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
}
