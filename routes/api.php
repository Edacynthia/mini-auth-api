<?php
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;



Route::post('/auth/signup', [AuthController::class, 'signup']);
Route::post('/auth/login', [AuthController::class, 'login']);


Route::middleware('auth.api')->post('/keys/create', [ApiKeyController::class, 'create']);
Route::middleware('auth.api')->post('/keys/revoke/{id}', [ApiKeyController::class, 'revoke']);

Route::middleware('auth.api')->get('/protected', function () {
    return ['message' => 'Access granted'];
});

