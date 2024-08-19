<?php

namespace Livesource\Chord\Filament\Forms;

use Filament\Forms\Components\Builder;
use LiveSource\Chord\Facades\Chord;

class PageBuilder extends Builder
{
    public function setUp(): void
    {
        parent::setUp();

        if (! $this->getBlocks()) {
            $blocks = collect(Chord::getBlockTypes())->map(function ($type) {
                return $type::getBuilderBlock();
            })->toArray();

            $this->blocks($blocks);
        }
    }
}
