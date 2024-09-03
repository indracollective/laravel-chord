<?php

namespace LiveSource\Chord\Http\Controllers;

use Illuminate\Routing\Controller;
use LiveSource\Chord\Models\ChordPage;
use Oddvalue\LaravelDrafts\Facades\LaravelDrafts;

class PageController extends Controller
{
    public function __invoke(string $path = '/')
    {
        // check if the preview query param is set...
        if (request()->has('preview') && auth()->check()) {
            LaravelDrafts::previewMode(true);
        }
        $page = ChordPage::firstWhere('path', $path);

        if (! $page) {
            abort(404);
        }

        return view('components.pages.page', ['page' => $page]);
    }
}
