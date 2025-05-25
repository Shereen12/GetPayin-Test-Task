<?php

use Illuminate\Support\Facades\Route;

// All routes should point to the main view
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');