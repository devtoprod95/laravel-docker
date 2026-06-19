<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('role')->group(function () {
    Route::middleware('admin:login')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', function () {
                return view('users');
            })->name('list');
        });

        Route::get('/settings/general', function () {
            return view('setting-general');
        });
    });

    Route::middleware('admin:guest')->group(function () {
        Route::get('/login', [LoginController::class, 'show'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
    });
});
