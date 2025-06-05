<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\categoriesController;
use App\Http\Controllers\servicesController;
use Illuminate\Http\Request; // Fixed: Correct Request class
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/login', [UserController::class, 'loginUser']);
Route::post('/auth/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Successfully logged out']);
})->middleware('auth:sanctum');
Route::get('/Categories', [categoriesController::class, 'index']);
Route::get('/Services', [servicesController::class, 'index']);