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

    Route::get('/all', [InventoryItemController::class, 'index']);
});

Route::prefix('genders')->group(function () {

    Route::get('/all', [GenderController::class, 'index']);
});

Route::prefix('survivors')->group(function () {

    Route::get('/all', [SurvivorController::class, 'index']);
    Route::get('/show/{survivor}', [SurvivorController::class, 'show']);
    Route::get('/inventory/{survivor}', [SurvivorController::class, 'inventory']);

    Route::post('/store', [SurvivorController::class, 'store']);
    Route::post('/update/{survivor}', [SurvivorController::class, 'update']);
});

Route::prefix('report-infections')->group(function () {

    Route::post('/report', [ReportInfectionController::class, 'reportInfection']);
});

Route::prefix('reports')->group(function () {

    Route::get('/percentage-infected', [ReportsController::class, 'percentageInfected']);
    Route::get('/percentage-not-infected', [ReportsController::class, 'percentageNotInfected']);
    Route::get('/average-items', [ReportsController::class, 'calculateAverageItemsQuantity']);
    Route::get('/total-points-lost', [ReportsController::class, 'calculateTotalPointsLost']);
});

Route::prefix('exchanges')->group(function () {

    Route::post('/trade', [ExchangeController::class, 'execTrade']);
});
