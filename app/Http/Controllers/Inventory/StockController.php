<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Display the stock levels.
     */
    public function index(Request $request)
    {
        $stats = [
            'total_items' => Lot::where('remaining_quantity', '>', 0)->sum('remaining_quantity'),
            'total_products' => Product::whereHas('lots', function($q) {
                $q->where('remaining_quantity', '>', 0);
            })->count(),
            'total_sku_count' => Product::count(),
            'low_stock' => Product::withSum('lots', 'remaining_quantity')
                ->having('lots_sum_remaining_quantity', '>', 0)
                ->having('lots_sum_remaining_quantity', '<', 10)
                ->get()->count()
        ];

        return view('pages.inventory.stocks.index', compact('stats'));
    }
}
