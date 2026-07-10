<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('role')->group(function () {
    Route::middleware('admin:login')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::delete('/dashboard/log', [DashboardController::class, 'deleteLog'])->name('dashboard.log.delete');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::prefix('role')->name('role.')->group(function () {
                Route::prefix('route')->name('route.')->group(function () {
                    Route::get('/', [AdminController::class, 'roleRouteIndex'])->name('index');
                    Route::get('/list', [AdminController::class, 'roleRouteList'])->name('list');
                    Route::get('/{id?}', [AdminController::class, 'roleRouteInfo'])->name('info');
                    Route::post('/store', [AdminController::class, 'roleRouteStore'])->name('store');
                    Route::delete('/delete', [AdminController::class, 'roleRouteDelete'])->name('delete');
                    Route::patch('/update', [AdminController::class, 'roleRouteUpdate'])->name('update');
                });

                Route::get('/', [AdminController::class, 'roleIndex'])->name('index');
                Route::get('/list', [AdminController::class, 'roleList'])->name('list');
                Route::get('/{id?}', [AdminController::class, 'roleInfo'])->name('info');
                Route::post('/store', [AdminController::class, 'roleStore'])->name('store');
                Route::delete('/delete', [AdminController::class, 'roleDelete'])->name('delete');
            });

            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::get('/list', [AdminController::class, 'list'])->name('list');
            Route::get('/view/{id?}', [AdminController::class, 'view'])->name('view');
            Route::post('/store', [AdminController::class, 'store'])->name('store');
            Route::delete('/delete', [AdminController::class, 'delete'])->name('delete');
            Route::patch('/active', [AdminController::class, 'updateActive'])->name('updateActive');

        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/icons', function () {
                return view('icons');
            })->name('icons');
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
        Route::get('/', [DashboardController::class, 'dashboard'])->name('/');
        Route::get('/login', [LoginController::class, 'show'])->name('show');
        Route::post('/login', [LoginController::class, 'login'])->name('login');

    });
});
