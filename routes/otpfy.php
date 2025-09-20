<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\Auth\VerifyAuthenticatedController;
use App\Http\Middleware\RequestInterseptor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return response()->json(["status" => 301, 'message' => 'Moved Permently'], 301);
});


Route::middleware([RequestInterseptor::class, 'guest:api'])->group(function () {
    Route::post('/login', AuthenticatedSessionController::class);
    Route::post('/register', RegisterUserController::class);
    Route::post('/verify-otp', VerifyAuthenticatedController::class);
});


Route::middleware([RequestInterseptor::class, 'auth:api'])->group(function () {
    Route::post('/main', function () {
        return  'You are in the main area.';
    });
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum', RequestInterseptor::class);
