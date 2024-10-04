<?php

namespace LiveSource\Chord\Http\Controllers;

use Illuminate\Routing\Controller;
use LiveSource\Chord\Models\ChordPage;

class PageController extends Controller
{
    public function __invoke(string $path = '/')
    {
        $page = ChordPage::firstWhere('path', $path);
        
        if (! $page) {
            abort(404);
        }

        return view('components.pages.page', ['page' => $page]);
    }
}
