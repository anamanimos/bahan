<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use Illuminate\Http\Request;

class PurchaseRequisitionController extends Controller
{
    /**
     * Display a listing of purchase requisitions.
     */
    public function index()
    {
        $stats = [
            'pending' => \App\Models\PurchaseRequisition::where('status', 'Submitted')->count(),
            'approved' => \App\Models\PurchaseRequisition::where('status', 'Approved')->count(),
            'spend' => \DB::table('purchase_requisition_items')
                ->join('purchase_requisitions', 'purchase_requisitions.id', '=', 'purchase_requisition_items.purchase_requisition_id')
                ->where('purchase_requisitions.status', 'Approved')
                ->sum(\DB::raw('purchase_requisition_items.requested_quantity * purchase_requisition_items.estimated_unit_price'))
        ];

        // Format spend
        $spend = $stats['spend'];
        if ($spend >= 1000000000) {
            $stats['spend_formatted'] = 'Rp ' . round($spend / 1000000000, 1) . 'M';
        } elseif ($spend >= 1000000) {
            $stats['spend_formatted'] = 'Rp ' . round($spend / 1000000, 1) . 'Jt';
        } elseif ($spend >= 1000) {
            $stats['spend_formatted'] = 'Rp ' . round($spend / 1000, 1) . 'Rb';
        } else {
            $stats['spend_formatted'] = 'Rp ' . number_format($spend, 0, ',', '.');
        }

        return view('pages.inventory.purchase-requisition.index', compact('stats'));
    }

    /**
     * Show the form for creating a new purchase requisition.
     */
    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('pages.inventory.purchase-requisition.create', compact('categories'));
    }

    /**
     * Store a newly created purchase requisition.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'supplier_id' => 'required|array',
            'supplier_id.*' => 'required|exists:suppliers,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0.01',
        ]);

        try {
            \DB::beginTransaction();

            // Generate PR number (PR-YYYYMMDD-XXXX)
            $date = date('Ymd');
            $latestPr = PurchaseRequisition::where('identifier', 'like', "PR-{$date}-%")
                ->orderBy('id', 'desc')
                ->first();
            
            $sequence = 1;
            if ($latestPr) {
                $parts = explode('-', $latestPr->identifier);
                $sequence = intval(end($parts)) + 1;
            }
            $identifier = sprintf("PR-%s-%04d", $date, $sequence);

            $pr = PurchaseRequisition::create([
                'identifier' => $identifier,
                'created_by_user_id' => auth()->id() ?? 1,
                'status' => 'Submitted',
                'notes' => $request->input('notes'),
            ]);

            $products = $request->input('product_id', []);
            $suppliers = $request->input('supplier_id', []);
            $quantities = $request->input('quantity', []);
            $prices = $request->input('price', []);
            $contextTypes = $request->input('context_type', []);
            $orderRefs = $request->input('order_reference', []);

            foreach ($products as $index => $productId) {
                // Remove formatting from price if needed (e.g. 1.500.000)
                $priceStr = $prices[$index] ?? '0';
                $price = floatval(str_replace(['.', ','], ['', '.'], $priceStr));
                
                $product = \App\Models\Product::find($productId);

                \App\Models\PurchaseRequisitionItem::create([
                    'purchase_requisition_id' => $pr->id,
                    'product_id' => $productId,
                    'supplier_id' => $suppliers[$index] ?? null,
                    'requested_quantity' => $quantities[$index] ?? 0,
                    'unit' => $product ? $product->base_unit : 'Pcs',
                    'estimated_unit_price' => $price,
                    'context' => $contextTypes[$index] ?? 'Stock',
                    'erp_order_reference' => $orderRefs[$index] ?? null,
                    'status' => 'Pending',
                ]);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan Beli berhasil disimpan (' . $identifier . ')',
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified purchase requisition.
     */
    public function show($id)
    {
        $requisition = PurchaseRequisition::with(['items.product', 'items.supplier'])
            ->where('identifier', $id)
            ->firstOrFail();
        return view('pages.inventory.purchase-requisition.show', compact('requisition'));
    }

    /**
     * Show verification page for the purchase requisition.
     */
    public function verify($id)
    {
        $requisition = PurchaseRequisition::with(['items.product', 'items.supplier'])
            ->where('identifier', $id)
            ->firstOrFail();
            
        return view('pages.inventory.purchase-requisition.verify', compact('requisition'));
    }

    /**
     * Update status of PR and its items.
     */
    public function updateStatus(Request $request, $id)
    {
        $requisition = PurchaseRequisition::where('identifier', $id)->firstOrFail();
        
        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses.'], 403);
        }

        \DB::transaction(function() use ($request, $requisition) {
            $itemStatuses = $request->input('item_status', []);
            $itemNotes = $request->input('item_notes', []);
            
            foreach ($requisition->items as $item) {
                $status = $itemStatuses[$item->id] ?? 'Pending';
                $notes = $itemNotes[$item->id] ?? $item->notes;
                
                $item->update([
                    'status' => $status,
                    'notes' => $notes
                ]);
            }

            // Update parent PR status based on item statuses
            $totalItems = $requisition->items()->count();
            $approvedCount = $requisition->items()->where('status', 'Approved')->count();
            $rejectedCount = $requisition->items()->where('status', 'Rejected')->count();
            
            if ($approvedCount === $totalItems) {
                $requisition->update(['status' => 'Approved']);
            } elseif ($approvedCount > 0) {
                $requisition->update(['status' => 'Partially Approved']);
            } elseif ($rejectedCount === $totalItems) {
                $requisition->update(['status' => 'Rejected']);
            } else {
                // If there are still pending items and no approvals, keep it as Submitted or set to Pending
                $requisition->update(['status' => 'Submitted']);
            }
        });

        return response()->json(['success' => true, 'message' => 'Verifikasi berhasil disimpan.']);
    }
}
