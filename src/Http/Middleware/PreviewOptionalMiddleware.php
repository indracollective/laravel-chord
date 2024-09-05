<?php

declare(strict_types=1);

namespace LiveSource\Chord\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Oddvalue\LaravelDrafts\Facades\LaravelDrafts;

class PreviewOptionalMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->isPreviewRequest($request)) {
            LaravelDrafts::previewMode();
        }

        return $next($request);
    }

    private function isPreviewRequest(Request $request): bool
    {
        $referer = $request->headers->get('referer');

        return $request->has('preview') || ($referer && str_contains($referer, '?preview'));
    }

    public function terminate(): void
    {
        LaravelDrafts::previewMode(false);
    }
}
