<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\Inventory\GoodsReceiptController;

Route::get('/', function () {
    return redirect('/inventory/goods-receipt');
});

Route::get('/dashboard', function () {
    return redirect('/inventory/goods-receipt');
})->name('dashboard');

Route::prefix('inventory')->group(function () {
    // Goods Receipt Routes
    Route::prefix('goods-receipt')->group(function () {
        Route::get('/', [GoodsReceiptController::class, 'index'])->name('inventory.goods-receipt.index');
        Route::get('/create', [GoodsReceiptController::class, 'create'])->name('inventory.goods-receipt.create');
        Route::post('/store', [GoodsReceiptController::class, 'store'])->name('inventory.goods-receipt.store');
    });

    // Purchase Requisition Routes
    Route::prefix('purchase-requisition')->group(function () {
        Route::get('/', [\App\Http\Controllers\Inventory\PurchaseRequisitionController::class, 'index'])->name('inventory.purchase-requisition.index');
        Route::get('/create', [\App\Http\Controllers\Inventory\PurchaseRequisitionController::class, 'create'])->name('inventory.purchase-requisition.create');
        Route::get('/{id}', [\App\Http\Controllers\Inventory\PurchaseRequisitionController::class, 'show'])->name('inventory.purchase-requisition.show');
    });

    // AJAX Routes
    Route::prefix('ajax/goods-receipt')->group(function () {
        Route::post('/list', [\App\Http\Controllers\Ajax\Inventory\GoodsReceiptAjaxController::class, 'list'])->name('inventory.ajax.goods-receipt.list');
        Route::get('/search-products', [\App\Http\Controllers\Ajax\Inventory\GoodsReceiptAjaxController::class, 'searchProducts'])->name('inventory.ajax.goods-receipt.search-products');
        Route::get('/search-suppliers', [\App\Http\Controllers\Ajax\Inventory\GoodsReceiptAjaxController::class, 'searchSuppliers'])->name('inventory.ajax.goods-receipt.search-suppliers');
        Route::get('/get-purchase-requisition', [\App\Http\Controllers\Ajax\Inventory\GoodsReceiptAjaxController::class, 'getPurchaseRequisition'])->name('inventory.ajax.goods-receipt.get-purchase-requisition');
    });

    Route::prefix('ajax/purchase-requisition')->group(function () {
        Route::post('/list', [\App\Http\Controllers\Ajax\Inventory\PurchaseRequisitionAjaxController::class, 'list'])->name('inventory.ajax.purchase-requisition.list');
    });
});

Route::prefix('master')->group(function () {
    Route::prefix('product')->group(function () {
        Route::get('/', [\App\Http\Controllers\Master\ProductController::class, 'index'])->name('master.product.index');
        Route::get('/create', [\App\Http\Controllers\Master\ProductController::class, 'create'])->name('master.product.create');
        Route::post('/store', [\App\Http\Controllers\Master\ProductController::class, 'store'])->name('master.product.store');
    });

    Route::prefix('category')->group(function () {
        Route::get('/', [\App\Http\Controllers\Master\CategoryController::class, 'index'])->name('master.category.index');
        Route::post('/store', [\App\Http\Controllers\Master\CategoryController::class, 'store'])->name('master.category.store');
    });

    // AJAX Routes for Master
    Route::prefix('ajax')->group(function () {
        Route::post('/product/list', [\App\Http\Controllers\Ajax\Master\ProductAjaxController::class, 'list'])->name('master.ajax.product.list');
        Route::post('/category/store', [\App\Http\Controllers\Ajax\Master\CategoryAjaxController::class, 'store'])->name('master.ajax.category.store');
        Route::post('/color/store', [\App\Http\Controllers\Ajax\Master\ColorAjaxController::class, 'store'])->name('master.ajax.color.store');
    });
});
