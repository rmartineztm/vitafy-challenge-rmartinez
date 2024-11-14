<?php

use App\Http\Controllers\LeadController;
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

Route::get('leads/{lead:uuid}', [LeadController::class, 'show']);
Route::post('leads', [LeadController::class, 'store']);
Route::put('leads/{lead:uuid}', [LeadController::class, 'update']);
Route::delete('leads/{lead:uuid}', [LeadController::class, 'destroy']);

Route::apiResource('leads', LeadController::class);