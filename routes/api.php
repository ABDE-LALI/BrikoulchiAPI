<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\categoriesController;
use App\Http\Controllers\servicesController;
use Illuminate\Http\Request; // Fixed: Correct Request class
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/login', [UserController::class, 'loginUser']);
Route::post('/auth/refresh', [UserController::class, 'refresh']);
Route::post('/updateUserInfo', [UserController::class, 'updateUser']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [UserController::class, 'logoutUser']);
    Route::get('/auth/profile', [UserController::class, 'index']);
    Route::get('/auth/isAuthenticated', function () {
        return true . 'hello';
    });
});
// Route::put('/updateUserInfo', function () {
//     return response()->json(['message' => 'Route is working']);
// });
// routes/api.php
Route::get('/Categories', [categoriesController::class, 'index']);
Route::get('/Services', [servicesController::class, 'index']);
Route::get('/ip-info', function () {
    $response = Http::get('https://ipapi.co/json/');
    return $response->json();
});
