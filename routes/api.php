<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\categoriesController;
use App\Http\Controllers\servicesController;
use Illuminate\Http\Request; // Fixed: Correct Request class
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/login', [UserController::class, 'loginUser']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [UserController::class, 'logoutUser']);
    Route::put('/updateUserInfo', [UserController::class, 'updateUser']);
});
// Route::put('/updateUserInfo', function () {
//     return response()->json(['message' => 'Route is working']);
// });
Route::get('/Categories', [categoriesController::class, 'index']);
Route::get('/Services', [servicesController::class, 'index']);
