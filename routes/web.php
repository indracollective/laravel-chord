<?php

use Illuminate\Support\Facades\Route;
use LiveSource\Chord\Http\Controllers\PageController;

Route::get('{url?}', PageController::class)
    ->where('url', '.*')
    ->name('chord.page')
    ->middleware('web');
