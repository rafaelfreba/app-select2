<?php

use App\Http\Controllers\Select2Controller;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/select2/{model}', [Select2Controller::class, 'index'])->name('select2.list');


