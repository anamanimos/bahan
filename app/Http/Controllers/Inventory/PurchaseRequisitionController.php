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
        return view('pages.inventory.purchase-requisition.index');
    }

    /**
     * Show the form for creating a new purchase requisition.
     */
    public function create()
    {
        return view('pages.inventory.purchase-requisition.create');
    }

    /**
     * Display the specified purchase requisition.
     */
    public function show($id)
    {
        $requisition = PurchaseRequisition::with(['items.product', 'items.supplier'])->findOrFail($id);
        return view('pages.inventory.purchase-requisition.show', compact('requisition'));
    }
}
