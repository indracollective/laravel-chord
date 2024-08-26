<?php

namespace LiveSource\Chord\Http\Controllers;

use Illuminate\Routing\Controller;
use LiveSource\Chord\Models\ChordPage;

class PageController extends Controller
{
    public function __invoke(string $path = 'home')
    {
        $page = ChordPage::firstWhere('path', $path);

        if (! $page) {
            abort(404);
        }

        return view('chord::page', ['page' => $page]);
    }
}
