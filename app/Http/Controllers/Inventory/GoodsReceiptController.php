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
            'in_progress' => \App\Models\PurchaseRequisition::where('status', 'Approved')->count(),
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
            }

            $goodsReceipt = \App\Models\GoodsReceipt::create([
                'identifier' => $identifier,
                'received_date' => $request->date,
                'supplier_id' => $request->supplier_id,
                'invoice_photo_path' => $invoicePath,
                'purchase_requisition_id' => $request->purchase_requisition_id,
            ]);

            foreach ($request->quantity as $index => $qty) {
                $productId = $request->product_id[$index];
                $price = $request->price[$index];
                $notes = $request->notes[$index] ?? null;

                $item = $goodsReceipt->items()->create([
                    'product_id' => $productId,
                    'received_quantity' => $qty,
                    'unit' => 'Mtr', // Should be dynamic from product
                    'unit_price' => $price,
                    'notes' => $notes,
                ]);

                // Create Lot for FIFO
                \App\Models\Lot::create([
                    'identifier' => $identifier . '-' . ($index + 1),
                    'product_id' => $productId,
                    'goods_receipt_item_id' => $item->id,
                    'initial_quantity' => $qty,
                    'remaining_quantity' => $qty,
                    'unit_cost' => $price,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Goods Receipt ' . $identifier . ' berhasil disimpan!',
                'redirect' => route('inventory.goods-receipt.index')
            ]);
        });
    }
}
