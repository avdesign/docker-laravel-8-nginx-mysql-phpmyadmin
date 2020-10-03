<?php

use Illuminate\Support\Facades\Route;


Route::get('/', [\App\Http\Controllers\PlaceController::class, 'index']);
