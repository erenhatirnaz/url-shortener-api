<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('json.response')->group(function () {
    Route::post('/register', [ApiAuthController::class, 'register'])->name('register.api');
    Route::post('/login', [ApiAuthController::class, 'login'])->name('login.api');
});

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [ApiAuthController::class, 'show'])->name('user');
});
