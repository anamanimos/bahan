<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index()
    {
        $today = date('Y-m-d');
        $stats = [
            'count_today' => Sale::whereDate('sale_date', $today)->count(),
            'amount_today' => Sale::whereDate('sale_date', $today)->sum('total_amount'),
            'total_month' => Sale::whereMonth('sale_date', date('m'))->whereYear('sale_date', date('Y'))->sum('total_amount'),
        ];
        
        $stats['amount_today_formatted'] = 'Rp ' . number_format($stats['amount_today'], 0, ',', '.');
        $stats['total_month_formatted'] = 'Rp ' . number_format($stats['total_month'], 0, ',', '.');

        return view('pages.sales.index', compact('stats'));
    }

    /**
     * Show the POS interface.
     */
    public function pos()
    {
        $customers = Customer::orderBy('name', 'asc')->get();
        
        $products = Product::with(['category', 'lots' => function($q) {
                $q->where('remaining_quantity', '>', 0)->orderBy('created_at', 'asc');
            }])
            ->withSum('lots', 'remaining_quantity')
            ->get();

        // Extract unique tags from specifications
        $tags = $products->flatMap(function($p) {
            return data_get($p->specifications, 'tags', []);
        })->unique()->filter()->sort()->values();

        return view('pages.sales.pos', compact('customers', 'products', 'tags'));
    }

    /**
     * Store a newly created sale in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.lot_id' => 'required|exists:lots,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.order_reference' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Generate Invoice Number
            $datePrefix = date('Ymd', strtotime($request->sale_date));
            $lastSale = Sale::where('invoice_number', 'like', "INV-{$datePrefix}-%")->orderBy('invoice_number', 'desc')->first();
            $nextNumber = 1;
            if ($lastSale) {
                $lastNumber = (int) substr($lastSale->invoice_number, -4);
                $nextNumber = $lastNumber + 1;
            }
            $invoiceNumber = "INV-{$datePrefix}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $request->customer_id,
                'order_reference' => $request->items[0]['order_reference'] ?? null, // Store main order reference
                'sale_date' => $request->sale_date,
                'status' => 'Paid',
                'payment_method' => $request->payment_method ?? 'Cash',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            $totalAmount = 0;

            foreach ($request->items as $item) {
                $lot = Lot::lockForUpdate()->findOrFail($item['lot_id']);
                
                if ($lot->remaining_quantity < $item['quantity']) {
                    throw new \Exception("Stok Lot {$lot->identifier} tidak mencukupi.");
                }

                $subtotal = $item['quantity'] * $item['unit_price'];
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'lot_id' => $item['lot_id'],
                    'order_reference' => $item['order_reference'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                ]);

                // Reduce stock
                $lot->decrement('remaining_quantity', $item['quantity']);
                $totalAmount += $subtotal;
            }

            $sale->update(['total_amount' => $totalAmount]);

            DB::commit();

            $sale->load(['customer', 'items.product', 'items.lot']);

            // Trigger ERP Webhook
            \App\Services\StockWebhookService::notify('create', 'sale', [
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'order_number' => $sale->order_reference, // ERP Order Number
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

            return response()->json([
                'success' => true,
                'message' => 'Penjualan berhasil disimpan.',
                'sale_id' => $sale->uuid,
                'redirect' => route('sales.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan penjualan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified sale.
     */
    public function show($uuid)
    {
        $sale = Sale::with(['customer', 'items.product', 'items.lot', 'creator'])->where('uuid', $uuid)->firstOrFail();
        return view('pages.sales.show', compact('sale'));
    }

    /**
     * Remove the specified sale from storage.
     */
    public function destroy($uuid)
    {
        try {
            DB::beginTransaction();

            $sale = Sale::with(['customer', 'items.product', 'items.lot'])->where('uuid', $uuid)->firstOrFail();

            // Notify ERP before deletion
            \App\Services\StockWebhookService::notify('delete', 'sale', [
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'customer' => [
                    'name' => $sale->customer->name,
                    'type' => $sale->customer->type,
                ],
                'items' => $sale->items->map(function($item) {
                    return [
                        'sku' => $item->product->sku,
                        'name' => $item->product->name,
                        'lot' => $item->lot->identifier ?? 'N/A',
                        'quantity' => $item->quantity,
                        'unit' => $item->product->base_unit,
                        'price' => $item->unit_price,
                        'subtotal' => $item->subtotal,
                    ];
                })
            ]);

            // Restore stock
            foreach ($sale->items as $item) {
                $lot = Lot::findOrFail($item->lot_id);
                $lot->increment('remaining_quantity', $item->quantity);
            }

            // Trigger Webhook for deletion
            \App\Services\StockWebhookService::notify('delete', 'sale', [
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'deleted_at' => now()->toIso8601String()
            ]);

            // Delete items and sale
            $sale->items()->delete();
            $sale->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penjualan berhasil dihapus dan stok telah dikembalikan ke Lot masing-masing.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus penjualan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print the specified sale invoice.
     */
    public function print($uuid)
    {
        $sale = Sale::with(['customer', 'items.product', 'items.lot', 'creator'])->where('uuid', $uuid)->firstOrFail();
        return view('pages.sales.print', compact('sale'));
    }
}
