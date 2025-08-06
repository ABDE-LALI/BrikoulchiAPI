<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\categoriesController;
use App\Http\Controllers\servicesController;
use App\Models\ServiceReview;
use Illuminate\Http\Request; // Fixed: Correct Request class
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/login', [UserController::class, 'loginUser']);
Route::post('/auth/refresh', [UserController::class, 'refresh']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [UserController::class, 'logoutUser']);
    Route::get('/auth/profile', [UserController::class, 'index']);
    Route::post('/updateUserInfo/{id}', [UserController::class, 'updateUser']);
    Route::post('/auth/createReview', [servicesController::class, 'createReview']);
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
Route::get('/user/index/', [UserController::class, 'index']);
Route::get('/Service/reviews/{id}', [servicesController::class, 'getReviews']);
Route::get('/ip-info', function () {
    $response = Http::get('https://ipapi.co/json/');
    return $response->json();
});
