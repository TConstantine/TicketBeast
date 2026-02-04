<?php

use App\Http\Controllers\ConcertOrdersController;
use App\Http\Controllers\ConcertsController;
use Illuminate\Support\Facades\Route;

Route::get('/concerts/{id}', [ConcertsController::class, 'show']);
Route::post('/concerts/{id}/orders', [ConcertOrdersController::class, 'store']);
