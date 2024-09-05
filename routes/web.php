<?php

use Illuminate\Support\Facades\Route;
use LiveSource\Chord\Http\Controllers\PageController;
use LiveSource\Chord\Http\Middleware\PreviewOptionalMiddleware;

Route::get('{path?}', PageController::class)
    ->where('path', '.*')
    ->name('chord.page')
    ->middleware([
        'web',
        PreviewOptionalMiddleware::class,
    ]);
