<?php

use LiveSource\Chord\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('{url?}', PageController::class)
    ->where('url', '.*')
    ->name('chord.page')
    ->middleware('web');
