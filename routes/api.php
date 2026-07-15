<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::any('/', [ApiController::class, 'log'])->name('');
