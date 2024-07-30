<?php

use App\Http\Controllers\AppInfoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CampaignManagedController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherGeneratedController;
use App\Http\Controllers\VoucherRewardController;
use App\Http\Controllers\VoucherRewardUsedController;
use App\Models\VoucherReward;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum']], function() {

    Route::get('/logout', [AuthController::class, 'logout']);

   /* APP-INFO */
    Route::prefix('app-info')->group(function() {
        Route::get('/', [AppInfoController::class, 'view']);
        Route::post('/', [AppInfoController::class, 'store']);
    });
    /* PROFILE */
    Route::prefix('profile')->group(function() {
        Route::get('/', [AuthController::class, 'view']);
        Route::post('/', [AuthController::class, 'update']);
        Route::post('/password', [AuthController::class, 'password']);
    });
    /* CAMPAIGN */
    Route::prefix('campaign')->group(function() {
        Route::get('/', [CampaignController::class, 'index']);
        Route::post('/', [CampaignController::class, 'store']);
        Route::get('/{id}', [CampaignController::class, 'view']);
        Route::post('/{id}', [CampaignController::class, 'update']);
        Route::delete('/{id}', [CampaignController::class, 'delete']);
    });
    Route::post('/campaign-store-by-points', [CampaignController::class, 'storeByPoints']);
    Route::get('/campaign-all', [CampaignController::class, 'indexAll']);
    Route::get('/campaign-list-by-user', [CampaignController::class, 'indexByUser']);
    /* CAMPAIGN MANAGED */
    Route::prefix('campaign-managed')->group(function() {
        Route::get('/', [CampaignManagedController::class, 'index']);
        Route::post('/', [CampaignManagedController::class, 'store']);
        Route::get('/{id}', [CampaignManagedController::class, 'view']);
        Route::post('/{id}', [CampaignManagedController::class, 'update']);
        Route::delete('/{id}', [CampaignManagedController::class, 'delete']);
    });
    Route::get('/campaign-managed-all', [CampaignManagedController::class, 'indexAll']);
    Route::post('/campaign-managed-status/{id}', [CampaignManagedController::class, 'statusUpdate']);
    Route::post('/campaign-managed-duration/{id}', [CampaignManagedController::class, 'durationUpdate']);
    Route::get('/campaign-managed-list-by-user', [CampaignManagedController::class, 'indexByUser']);
    Route::get('/campaign-managed-list-by-user-active', [CampaignManagedController::class, 'indexByUserActive']);
    /* ROLE */
    Route::prefix('role')->group(function() {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{id}', [RoleController::class, 'view']);
        Route::post('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'delete']);
    });
    Route::get('/role-all', [RoleController::class, 'indexAll']);
    /**
     * PRICE 
     **/
    Route::prefix('price')->group(function() {
        Route::get('/', [PriceController::class, 'index']);
        Route::post('/', [PriceController::class, 'store']);
        Route::get('/{id}', [PriceController::class, 'view']);
        Route::post('/{id}', [PriceController::class, 'update']);
        Route::delete('/{id}', [PriceController::class, 'delete']);
    });
    Route::get('/price-all', [PriceController::class, 'indexAll']);
    Route::get('/price-priority-one', [PriceController::class, 'priorityOne']);
    /**
     * USER 
     **/
    Route::prefix('user')->group(function() {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'view']);
        Route::post('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'delete']);
    });
    Route::get('/user-all', [UserController::class, 'indexAll']);
    /** 
     * VOUCHER-GENERATED 
     **/
    Route::prefix('voucher-generated')->group(function() {
        Route::get('/', [VoucherGeneratedController::class, 'index']);
        Route::post('/', [VoucherGeneratedController::class, 'store']);
        Route::get('/{id}', [VoucherGeneratedController::class, 'view']);
        Route::delete('/{id}', [VoucherGeneratedController::class, 'delete']);
    });
    Route::get('/voucher-generated-search-by-code', [VoucherGeneratedController::class, 'searchByCode']);
    Route::post('/voucher-generated-store-all', [VoucherGeneratedController::class, 'storeAll']);
    Route::get('/voucher-generated-by-user', [VoucherGeneratedController::class, 'indexByUser']);
    Route::get('/voucher-generated-by-campaign/{id}', [VoucherGeneratedController::class, 'indexByCampaign']);
    Route::get('/voucher-generated-by-campaign-csv/{id}', [VoucherGeneratedController::class, 'indexByCampaignCSV']);
    Route::delete('/voucher-generated-by-campaign', [VoucherGeneratedController::class, 'deleteAllByCampaign']);
    Route::get('/voucher-generated-check-by-campaign', [VoucherGeneratedController::class, 'checkVoucherByCampaignManagedId']);
    /* VOUCHER REWARD USED */
    Route::prefix('voucher-reward-used')->group(function() {
        Route::get('/', [VoucherRewardUsedController::class, 'index']);
        Route::post('/', [VoucherRewardUsedController::class, 'store']);
        Route::get('/{id}', [VoucherRewardUsedController::class, 'view']);
        Route::delete('/{id}', [VoucherRewardUsedController::class, 'delete']);
    });
    Route::get('/index-by-user', [VoucherRewardUsedController::class, 'indexByUser']);
    Route::get('/view-by-user', [VoucherRewardUsedController::class, 'viewByUser']);
    /* CAMPAIGN MANAGED */
    Route::prefix('voucher-reward')->group(function() {
        Route::get('/{id}', [VoucherRewardController::class, 'view']);
    });
    Route::get('/voucher-reward-by-campaign/{id}', [VoucherRewardController::class, 'indexByCampaign']);
    Route::get('/voucher-reward-search-by-code', [VoucherRewardController::class, 'searchByCode']);


});

