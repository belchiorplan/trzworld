<?php

use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\GenderController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\ReportInfectionController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SurvivorController;
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

Route::prefix('inventory-items')->group(function () {

    Route::get('/', [InventoryItemController::class, 'index']);
});

Route::prefix('genders')->group(function () {

    Route::get('/', [GenderController::class, 'index']);
});

Route::prefix('survivors')->group(function () {

    Route::get('/', [SurvivorController::class, 'index']);
    Route::get('/{survivor}', [SurvivorController::class, 'show']);
    Route::get('/{survivor}/inventory', [SurvivorController::class, 'inventory']);

    Route::post('/', [SurvivorController::class, 'store']);
    Route::patch('/{survivor}', [SurvivorController::class, 'update']);
});

Route::prefix('report-infections')->group(function () {

    Route::post('/report', [ReportInfectionController::class, 'reportInfection']);
});

Route::prefix('reports')->group(function () {

    Route::get('/percentage-infection', [ReportsController::class, 'percentageInfectedOrNotInfected']);
    Route::get('/average-items', [ReportsController::class, 'calculateAverageItemsQuantity']);
    Route::get('/total-points-lost', [ReportsController::class, 'calculateTotalPointsLost']);
});

Route::prefix('exchanges')->group(function () {

    Route::post('/trade', [ExchangeController::class, 'execTrade']);
});
