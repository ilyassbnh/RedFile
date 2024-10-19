<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('app');
});
// In routes/web.php
Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');