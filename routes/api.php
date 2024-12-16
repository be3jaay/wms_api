<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaterReadingController;
use App\Http\Controllers\AnomalyController;

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

Route::post('/v1/base-content/mutate-water-readings', [WaterReadingController::class, 'store']);

Route::get('/v1/base-content/get-all-water-readings', [WaterReadingController::class, 'index']);

Route::get('/v1/base-content/current-water-readings', [WaterReadingController::class, 'getCurrentWaterReading']);

Route::get('/v1/base-content/show-readings-daily', [WaterReadingController::class, 'showReadingPerDay']);

Route::get('/v1/base-content/show-readings-weekly', [WaterReadingController::class, 'showReadingPerWeek']);

Route::get('/v1/base-content/show-notification-daily', [AnomalyController::class, 'showAnomalyNotifications']);

Route::get('/v1/base-content/get-water-reading-range', [WaterReadingController::class, 'getWaterReadingRange']);

Route::delete('/v1/base-content/delete-selected-anomaly', [AnomalyController::class, 'clearSelectedNotifications']);

Route::delete('/v1/base-content/delete-all-anomaly', [AnomalyController::class, 'deleteAllAnomalies']);

Route::delete('/v1/base-content/delete-anomaly/{id}', [AnomalyController::class, 'deleteAnomaly']);

Route::delete('/v1/base-content/delete-daily-anomaly', [AnomalyController::class, 'deleteDailyAnomaly']);

Route::get('/v1/base-content/show-anomalies', [AnomalyController::class, 'showAnomalies']);
