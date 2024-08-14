<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Resources\Pages\Page;
use Illuminate\View\View;
use LiveSource\Chord\Filament\Resources\PageResource;

class TestPage extends Page
{
    protected static string $resource = PageResource::class;

    protected static string $view = 'chord::cms.test-page';

    protected array $extraBodyAttributes = ['class' => 'chord'];

    public function render(): View
    {
        return view('chord::cms.test-page');
    }
}
