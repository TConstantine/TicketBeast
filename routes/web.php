<?php

use App\Http\Controllers\ConcertsController;
use Illuminate\Support\Facades\Route;

Route::get('/concerts/{id}', [ConcertsController::class, 'show']);
