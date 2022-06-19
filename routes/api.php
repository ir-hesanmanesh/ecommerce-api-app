<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\BrandController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\PaymentController;
use App\Http\Controllers\V1\ProductController;
use App\Http\Controllers\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/users', [UserController::class, 'index']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        Route::apiResource('/brands', BrandController::class);
        Route::get('/brands/{brand}/products', [BrandController::class, 'products']);

        Route::apiResource('/categories', CategoryController::class);
        Route::get('/categories/{category}/children', [CategoryController::class, 'children']);
        Route::get('/categories/{category}/parent', [CategoryController::class, 'parent']);
        Route::get('/categories/{category}/products', [CategoryController::class, 'products']);

        Route::apiResource('/products', ProductController::class);

        Route::post('/payment/send', [PaymentController::class, 'send']);

        Route::post('/payment/verify', [PaymentController::class, 'verify']);
    });
    

    Route::post('/register', [AuthController::class, 'register']);
});
