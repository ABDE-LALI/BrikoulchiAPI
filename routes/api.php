<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\categoriesController;
use App\Http\Controllers\Api\InitialServicesController;
use App\Http\Controllers\Api\servicesController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Middleware\RemouveReview;
use App\Http\Middleware\RemouveService;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/login', [UserController::class, 'loginUser']);
Route::post('/auth/refresh', [UserController::class, 'refresh']);
Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/chat', [ChatController::class, 'index']);
    // Route::post('/chat', [ChatController::class, 'store']);
    Route::get('/auth/fetchMessages', [ConversationController::class, 'conversationMessages']);
    Route::get('/auth/fetchConversations', [ConversationController::class, 'index']);
    Route::post('/auth/sendMessage', [ChatController::class, 'store']);
    Route::post('/auth/logout', [UserController::class, 'logoutUser']);
    Route::post('/auth/updateUserInfo/{id}', [UserController::class, 'updateUser']);
    Route::post('/auth/createReview', [servicesController::class, 'createReview']);
    Route::post('/auth/ReactWithLike/{reviewId}', [servicesController::class, 'ReactWithLike']);
    Route::post('/auth/RemouveReview/{reviewId}', [servicesController::class, 'RemouveReview'])->middleware(RemouveReview::class);
    Route::post('/auth/delete/service/{id}', [servicesController::class, 'deleteService'])->middleware(RemouveService::class);
    Route::post('/auth/edit/service/{id}', [servicesController::class, 'editService'])->middleware(RemouveService::class);
    Route::get('/auth/isAuthenticated', function () {
        return true . 'hello';
    });
    Route::post('/auth/create/service', [servicesController::class, 'createService']);
});
// Route::put('/updateUserInfo', function () {
//     return response()->json(['message' => 'Route is working']);
// });
// routes/api.php
Route::get('/profile/{username}', [UserController::class, 'index']);
Route::get('/Categories/{withGlobalServices?}', [categoriesController::class, 'index']);
Route::get('/Services/{userId?}', [servicesController::class, 'index']);
Route::get('/GServices/{globalserviceId?}', [InitialServicesController::class, 'getInitialServices']);
Route::get('/user/index/', [UserController::class, 'index']);
Route::get('/Service/reviews/{id}', [servicesController::class, 'getReviews']);
Route::get('/ip-info', function () {
    $response = Http::get('https://ipapi.co/json/');
    return $response->json();
});

Broadcast::routes();