<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParseController;

Route::post('/message', [ParseController::class, 'getMessage']);
