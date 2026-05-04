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
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\ApiTokenController;
use App\Http\Controllers\CompanionController;
use App\Http\Controllers\CompanionUploadController;

// Companion Camera routes (public — phone accesses via QR token, no auth)
Route::get('/cam/{token}', [CompanionController::class, 'show'])->name('companion.camera');
Route::post('/companion/upload', [CompanionUploadController::class, 'store'])->name('companion.upload');
Route::get('/companion/heartbeat/{token}', [CompanionController::class, 'heartbeat'])->name('companion.heartbeat');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::get('/auth/sso/redirect', [AuthController::class, 'redirectToERP'])->name('sso.redirect');
Route::get('/auth/sso', [AuthController::class, 'handleSSOCallback'])->name('sso.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'check.access'])->group(function () {
    Route::post('/claim-admin', [\App\Http\Controllers\AdminClaimController::class, 'claim']);

    // Companion Camera (PC-side, authenticated)
    Route::get('/companion/check-session', [CompanionController::class, 'checkSession'])->name('companion.session.check');
    Route::post('/companion/session', [CompanionController::class, 'createSession'])->name('companion.session.create');
    Route::get('/companion/check-photo/{token}', [CompanionController::class, 'checkPhoto'])->name('companion.check-photo');
    Route::post('/companion/clear-photo/{token}', [CompanionController::class, 'clearPhoto'])->name('companion.clear-photo');
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::post('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'store']);
        Route::put('/admin/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::delete('/admin/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'destroy']);

        Route::get('/admin/api/token', [ApiTokenController::class, 'index']);
        Route::post('/admin/api/token', [ApiTokenController::class, 'store']);
        Route::delete('/admin/api/token/{id}', [ApiTokenController::class, 'destroy']);

        Route::prefix('admin/webhook')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WebhookController::class, 'index'])->name('admin.webhook.index');
            Route::get('/create', [\App\Http\Controllers\Admin\WebhookController::class, 'create'])->name('admin.webhook.create');
            Route::post('/store', [\App\Http\Controllers\Admin\WebhookController::class, 'store'])->name('admin.webhook.store');
            Route::get('/edit/{webhook}', [\App\Http\Controllers\Admin\WebhookController::class, 'edit'])->name('admin.webhook.edit');
            Route::put('/update/{webhook}', [\App\Http\Controllers\Admin\WebhookController::class, 'update'])->name('admin.webhook.update');
            Route::delete('/delete/{webhook}', [\App\Http\Controllers\Admin\WebhookController::class, 'destroy'])->name('admin.webhook.destroy');
            Route::get('/documentation', [\App\Http\Controllers\Admin\WebhookController::class, 'documentation'])->name('admin.webhook.documentation');
        });
    });

    Route::get('/', function () {
        return redirect('/dashboard');
    });

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

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
            Route::post('/store', [\App\Http\Controllers\Inventory\PurchaseRequisitionController::class, 'store'])->name('inventory.purchase-requisition.store');
            Route::get('/{id}', [\App\Http\Controllers\Inventory\PurchaseRequisitionController::class, 'show'])->name('inventory.purchase-requisition.show');
            Route::get('/verify/{id}', [\App\Http\Controllers\Inventory\PurchaseRequisitionController::class, 'verify'])->name('inventory.purchase-requisition.verify');
            Route::post('/update-status/{id}', [\App\Http\Controllers\Inventory\PurchaseRequisitionController::class, 'updateStatus'])->name('inventory.purchase-requisition.update-status');
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
            Route::get('/search-orders', [\App\Http\Controllers\Ajax\External\DamaiJayaApiController::class, 'searchOrders'])->name('inventory.ajax.purchase-requisition.search-orders');
        });

        Route::prefix('ajax/stocks')->group(function () {
            Route::post('/list', [\App\Http\Controllers\Ajax\Inventory\StockAjaxController::class, 'list'])->name('inventory.ajax.stocks.list');
        });

        // Stocks Routes
        Route::prefix('stocks')->group(function () {
            Route::get('/', [\App\Http\Controllers\Inventory\StockController::class, 'index'])->name('inventory.stocks.index');
        });
    });

    Route::prefix('master')->group(function () {
        Route::prefix('product')->group(function () {
            Route::get('/', [\App\Http\Controllers\Master\ProductController::class, 'index'])->name('master.product.index');
            Route::get('/create', [\App\Http\Controllers\Master\ProductController::class, 'create'])->name('master.product.create');
            Route::post('/store', [\App\Http\Controllers\Master\ProductController::class, 'store'])->name('master.product.store');
            Route::get('/edit/{id}', [\App\Http\Controllers\Master\ProductController::class, 'edit'])->name('master.product.edit');
            Route::put('/update/{id}', [\App\Http\Controllers\Master\ProductController::class, 'update'])->name('master.product.update');
            Route::post('/export', [\App\Http\Controllers\Master\ProductController::class, 'exportCsv'])->name('master.product.export');
            Route::post('/import', [\App\Http\Controllers\Master\ProductController::class, 'importCsv'])->name('master.product.import');
            Route::get('/download-template', [\App\Http\Controllers\Master\ProductController::class, 'downloadTemplate'])->name('master.product.template');
        });

        Route::prefix('supplier')->group(function () {
            Route::get('/', [\App\Http\Controllers\Master\SupplierController::class, 'index'])->name('master.supplier.index');
            Route::get('/create', [\App\Http\Controllers\Master\SupplierController::class, 'create'])->name('master.supplier.create');
            Route::get('/edit/{id}', [\App\Http\Controllers\Master\SupplierController::class, 'edit'])->name('master.supplier.edit');
        });

        Route::prefix('category')->group(function () {
            Route::get('/', [\App\Http\Controllers\Master\CategoryController::class, 'index'])->name('master.category.index');
            Route::post('/store', [\App\Http\Controllers\Master\CategoryController::class, 'store'])->name('master.category.store');
        });

        Route::prefix('colors')->group(function () {
            Route::get('/', [\App\Http\Controllers\Master\ColorController::class, 'index'])->name('master.color.index');
            Route::get('/export', [\App\Http\Controllers\Master\ColorController::class, 'exportCsv'])->name('master.color.export');
            Route::get('/template', [\App\Http\Controllers\Master\ColorController::class, 'downloadTemplate'])->name('master.color.template');
        });

        Route::prefix('customer')->group(function () {
            Route::get('/', [\App\Http\Controllers\Master\CustomerController::class, 'index'])->name('master.customer.index');
            Route::get('/create', [\App\Http\Controllers\Master\CustomerController::class, 'create'])->name('master.customer.create');
            Route::post('/store', [\App\Http\Controllers\Master\CustomerController::class, 'store'])->name('master.customer.store');
            Route::get('/edit/{customer}', [\App\Http\Controllers\Master\CustomerController::class, 'edit'])->name('master.customer.edit');
            Route::put('/update/{customer}', [\App\Http\Controllers\Master\CustomerController::class, 'update'])->name('master.customer.update');
            Route::delete('/delete/{customer}', [\App\Http\Controllers\Master\CustomerController::class, 'destroy'])->name('master.customer.destroy');
        });

        // AJAX Routes for Master
        Route::prefix('ajax')->group(function () {
            Route::post('/product/list', [\App\Http\Controllers\Ajax\Master\ProductAjaxController::class, 'list'])->name('master.ajax.product.list');
            Route::post('/product/duplicate', [\App\Http\Controllers\Ajax\Master\ProductAjaxController::class, 'duplicate'])->name('master.ajax.product.duplicate');
            Route::post('/product/delete', [\App\Http\Controllers\Ajax\Master\ProductAjaxController::class, 'destroy'])->name('master.ajax.product.delete');
            Route::post('/product/merge', [\App\Http\Controllers\Ajax\Master\ProductAjaxController::class, 'merge'])->name('master.ajax.product.merge');
            
            Route::post('/supplier/list', [\App\Http\Controllers\Ajax\Master\SupplierAjaxController::class, 'list'])->name('master.ajax.supplier.list');
            Route::post('/supplier/store', [\App\Http\Controllers\Ajax\Master\SupplierAjaxController::class, 'store'])->name('master.ajax.supplier.store');
            Route::post('/supplier/update/{id}', [\App\Http\Controllers\Ajax\Master\SupplierAjaxController::class, 'update'])->name('master.ajax.supplier.update');
            Route::post('/supplier/delete', [\App\Http\Controllers\Ajax\Master\SupplierAjaxController::class, 'destroy'])->name('master.ajax.supplier.delete');
            Route::post('/supplier/merge', [\App\Http\Controllers\Ajax\Master\SupplierAjaxController::class, 'merge'])->name('master.ajax.supplier.merge');
            Route::post('/category/store', [\App\Http\Controllers\Ajax\Master\CategoryAjaxController::class, 'store'])->name('master.ajax.category.store');
            Route::post('/color/list', [\App\Http\Controllers\Ajax\Master\ColorAjaxController::class, 'list'])->name('master.ajax.color.list');
            Route::post('/color/store', [\App\Http\Controllers\Ajax\Master\ColorAjaxController::class, 'store'])->name('master.ajax.color.store');
            Route::post('/color/update/{id}', [\App\Http\Controllers\Ajax\Master\ColorAjaxController::class, 'update'])->name('master.ajax.color.update');
            Route::post('/color/delete', [\App\Http\Controllers\Ajax\Master\ColorAjaxController::class, 'destroy'])->name('master.ajax.color.delete');
            Route::post('/color/import/validate', [\App\Http\Controllers\Ajax\Master\ColorAjaxController::class, 'validateImport'])->name('master.ajax.color.import.validate');
            Route::post('/color/import/confirm', [\App\Http\Controllers\Ajax\Master\ColorAjaxController::class, 'confirmImport'])->name('master.ajax.color.import.confirm');
        });
    });

    // Sales AJAX
    Route::prefix('ajax/sales')->group(function () {
        Route::post('/list', [\App\Http\Controllers\Ajax\Sales\SaleAjaxController::class, 'list'])->name('sales.ajax.list');
        Route::get('/search-products', [\App\Http\Controllers\Ajax\Sales\SaleAjaxController::class, 'searchProducts'])->name('sales.ajax.search-products');
        Route::post('/customer/store', [\App\Http\Controllers\Ajax\Sales\SaleAjaxController::class, 'storeCustomer'])->name('sales.ajax.customer.store');
    });

    // Sales Routes
    Route::group(['prefix' => 'sales'], function () {
        Route::get('/', [\App\Http\Controllers\Sales\SaleController::class, 'index'])->name('sales.index');
        Route::post('/store', [\App\Http\Controllers\Sales\SaleController::class, 'store'])->name('sales.store');
        Route::get('/print/{id}', [\App\Http\Controllers\Sales\SaleController::class, 'print'])->name('sales.print');
        Route::get('/{id}', [\App\Http\Controllers\Sales\SaleController::class, 'show'])->name('sales.show');
        Route::delete('/{id}', [\App\Http\Controllers\Sales\SaleController::class, 'destroy'])->name('sales.destroy');
    });

    Route::get('/pos', [\App\Http\Controllers\Sales\SaleController::class, 'pos'])->name('sales.pos');
});