<?php

namespace LiveSource\Chord\Http\Controllers;

//use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use LiveSource\Chord\Models\ChordPage;

class PageController extends Controller
{
    public function __invoke(string $url = '/')
    {
        $page = ChordPage::firstWhere('slug', $url);
        if (! $page) {
            abort(404);
        }

        return view('chord::components.site.page.index', [
            'page' => $page,
        ]);
    }
}
