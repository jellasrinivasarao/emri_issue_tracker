<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Api\OperationalStatusController;

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

Route::post('/integration/token', [OperationalStatusController::class, 'generateToken']);
Route::post('/integration/operational-dashboard/status', [OperationalStatusController::class, 'updateStatus'])
    ->middleware('api.token');
