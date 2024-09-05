<?php

declare(strict_types=1);

namespace LiveSource\Chord\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Oddvalue\LaravelDrafts\Facades\LaravelDrafts;
use Symfony\Component\HttpFoundation\Response;

class PreviewMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        LaravelDrafts::previewMode(true);

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        LaravelDrafts::previewMode(false);
    }
}
