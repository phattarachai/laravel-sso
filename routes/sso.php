<?php

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;
use Phattarachai\Sso\Http\Controllers\CallbackController;
use Phattarachai\Sso\Http\Controllers\LoginController;
use Phattarachai\Sso\Http\Controllers\LogoutController;
use Phattarachai\Sso\Http\Controllers\SignedOutController;

Route::middleware('web')->group(function (): void {
    Route::get('/login', LoginController::class)
        ->middleware(RedirectIfAuthenticated::class)
        ->name('login');

    Route::get('/auth/sso/callback', CallbackController::class)
        ->middleware(RedirectIfAuthenticated::class)
        ->name('sso.callback');

    Route::post('/logout', LogoutController::class)->name('logout');

    Route::get('/signed-out', SignedOutController::class)
        ->middleware(RedirectIfAuthenticated::class)
        ->name('sso.signed-out');
});
