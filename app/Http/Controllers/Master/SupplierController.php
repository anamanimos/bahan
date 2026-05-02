<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Supplier::count(),
            'total_this_month' => Supplier::whereMonth('created_at', now()->month)->count(),
            'total_deleted' => Supplier::onlyTrashed()->count(),
        ];
        return view('pages.master.supplier.index', compact('stats'));
    }

    public function create()
    {
        return view('pages.master.supplier.create');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('pages.master.supplier.edit', compact('supplier'));
    }
}
