<?php

namespace LiveSource\Chord\Filament\Forms;

use Filament\Forms\Components\Builder;
use LiveSource\Chord\Facades\Chord;

class PageBuilder extends Builder
{
    public function setUp(): void
    {
        parent::setUp();

        $this->extraFieldWrapperAttributes(['class' => 'chord-page-builder'], true);
        $this->collapsible(true);
        //$this->collapsed(true);
        $blocks = collect(Chord::getBlockTypes())->map(function ($type) {
            return $type::getBuilderBlock();
        })->toArray();

        $this->blocks($blocks);
    }
}
