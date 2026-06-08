<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/users', function () {
    return view('users');
});

Route::get('/settings/general', function () {
    return view('setting-general');
});
