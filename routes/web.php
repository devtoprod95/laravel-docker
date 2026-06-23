<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ManageController;
use Illuminate\Support\Facades\Route;

Route::middleware('role')->group(function () {
    Route::middleware('admin:login')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::prefix('manage')->name('manage.')->group(function () {
            Route::get('/', [ManageController::class, 'index'])->name('index');
            Route::get('/list', [ManageController::class, 'list'])->name('list');
            Route::get('/view/{id?}', [ManageController::class, 'view'])->name('view');
            Route::post('/store', [ManageController::class, 'store'])->name('store');
            Route::delete('/delete', [ManageController::class, 'delete'])->name('delete');
            Route::patch('/active', [ManageController::class, 'updateActive'])->name('updateActive');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/general', function () {
                return view('setting-general');
            })->name('general');
            Route::get('/security', function () {
                return abort(404);
            })->name('security');

            Route::prefix('notifications')->name('notifications.')->group(function () {
                Route::get('/email', function () {
                    return abort(404);
                })->name('email');
                Route::get('/sms', function () {
                    return abort(404);
                })->name('sms');
            });

        });
    });

    Route::middleware('admin:guest')->group(function () {
        Route::get('/', function () {
            return view('icons');
        })->name('/');
        Route::get('/login', [LoginController::class, 'show'])->name('show');
        Route::post('/login', [LoginController::class, 'login'])->name('login');

    });
});
