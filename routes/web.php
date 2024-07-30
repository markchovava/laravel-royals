<?php

use App\Http\Controllers\AppInfoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignManagedController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\VoucherGeneratedController;
use App\Http\Controllers\VoucherRewardController;
use App\Models\AppInfo;
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

Route::get('/', function () {
    return view('welcome');
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
/* APP-INFO */
Route::prefix('app-info')->group(function() {
    Route::get('/', [AppInfoController::class, 'view']);
    Route::post('/', [AppInfoController::class, 'store']);
});
/* CAMPAIGN */
Route::prefix('campaign')->group(function() {
    Route::get('/', [CampaignController::class, 'index']);
    Route::get('/{id}', [CampaignController::class, 'view']);
});
Route::get('/campaign-all', [CampaignController::class, 'indexAll']);
/* CAMPAIGN-MANAGED */
Route::prefix('campaign-managed')->group(function() {
    Route::get('/', [CampaignManagedController::class, 'index']);
    Route::get('/{id}', [CampaignManagedController::class, 'view']);
});
Route::get('/campaign-managed-all', [CampaignManagedController::class, 'indexAll']);
Route::post('/campaign-managed-status/{id}', [CampaignManagedController::class, 'statusUpdate']);
/* ROLE */
Route::prefix('role')->group(function() {
    Route::get('/', [RoleController::class, 'index']);
    Route::get('/{id}', [RoleController::class, 'view']);
});
Route::get('/role-all', [RoleController::class, 'indexAll']);
/* PRICE */
Route::prefix('price')->group(function() {
    Route::get('/', [PriceController::class, 'index']);
    Route::get('/{id}', [PriceController::class, 'view']);
});
Route::get('/price-all', [PriceController::class, 'indexAll']);
Route::get('/price-priority-one', [PriceController::class, 'priorityOne']);
/* VOUCHER-GENERATED */
Route::get('/voucher-generated-search-by-code', [VoucherGeneratedController::class, 'searchByCode']);

/* CAMPAIGN MANAGED */
Route::prefix('voucher-reward')->group(function() {
    Route::get('/{id}', [VoucherRewardController::class, 'view']);
});
Route::get('/voucher-reward-by-campaign/{id}', [VoucherRewardController::class, 'indexByCampaign']);
Route::get('/voucher-reward-search-by-code', [VoucherRewardController::class, 'searchByCode']);