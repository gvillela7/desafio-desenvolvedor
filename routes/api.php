<?php

use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\Upload\UploadController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function () {
    Route::prefix('user')->namespace('Auth')->group(function () {
        Route::post('/create', [UserAuthController::class, 'create']);
        Route::post('/login', [UserAuthController::class, 'login'])->name('login');
    });
});

Route::middleware(['auth:sanctum', 'throttle'])->group(function () {
    Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function () {
        Route::prefix('upload')->namespace('Upload')->group(function () {
            Route::post('/save', [UploadController::class, 'save']);
            Route::post('/log', [UploadController::class, 'log']);
            Route::get('/getData', [UploadController::class, 'getData']);
            Route::delete('/delete/{id}', [UploadController::class, 'delete']);
        });
    });
});
