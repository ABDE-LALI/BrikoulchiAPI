<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; 

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/api/auth/login', [UserController::class, 'loginUser']);
// Route::post('/api/auth/register', [UserController::class, 'createUser']);
// Route::middleware('auth:sanctum')->get('/user', function () {
//     return request()->user();
// });
