<?php

use Illuminate\Support\Facades\Route;
use Indra\Revisor\Middleware\DraftableMiddleware;
use LiveSource\Chord\Http\Controllers\PageController;

Route::get('{path?}', PageController::class)
    ->where('path', '.*')
    ->name('chord.page')
    ->middleware([
        'web',
        DraftableMiddleware::class,
    ]);
