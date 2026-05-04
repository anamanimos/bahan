<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\GoodsReceipt;
use App\Models\PurchaseRequisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // If user has no roles, show the "Access Denied / Claim Admin" dashboard
        if (auth()->user()->roles->count() === 0) {
            return view('dashboard');
        }

        // Stats
        $stats = [
            'total_products' => Product::count(),
            'total_suppliers' => Supplier::count(),
            'total_receipts_month' => GoodsReceipt::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'pending_pr' => PurchaseRequisition::where('status', 'Submitted')->count(),
        ];

        // Recent Goods Receipts
        $recent_receipts = GoodsReceipt::with('supplier')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Goods Receipt Trend (Last 7 days)
        $receipt_trend = GoodsReceipt::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $trend_data = [
            'labels' => [],
            'values' => []
        ];

        foreach ($receipt_trend as $item) {
            $trend_data['labels'][] = Carbon::parse($item->date)->format('d M');
            $trend_data['values'][] = $item->total;
        }

        return view('pages.dashboard.index', compact('stats', 'recent_receipts', 'trend_data'));
    }
}
