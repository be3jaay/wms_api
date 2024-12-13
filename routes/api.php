<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaterReadingController;

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
