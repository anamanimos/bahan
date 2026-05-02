<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\Api\ProductController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::post('/products', [\App\Http\Controllers\Api\ProductController::class, 'store']);
    Route::get('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'show']);
    Route::put('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'update']);

    Route::get('/products/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::post('/products/categories', [\App\Http\Controllers\Api\CategoryController::class, 'store']);
    Route::put('/products/categories/{id}', [\App\Http\Controllers\Api\CategoryController::class, 'update']);
});
