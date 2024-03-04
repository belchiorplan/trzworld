<?php

use App\Http\Controllers\ReportInfectionController;
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

Route::prefix('survivor')->group(function () {

    Route::get('/all', [SurvivorController::class, 'index']);
    Route::get('/show/{survivor}', [SurvivorController::class, 'show']);

    Route::post('/store', [SurvivorController::class, 'store']);
    Route::post('/update/{survivor}', [SurvivorController::class, 'update']);
});

Route::prefix('report-infection')->group(function () {

    Route::post('/report', [ReportInfectionController::class, 'reportInfection']);
});
