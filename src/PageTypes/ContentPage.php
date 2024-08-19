<?php

namespace Livesource\Chord\PageTypes;

use Illuminate\Support\Collection;
use LiveSource\Chord\Chord;
use Livesource\Chord\Filament\Forms\PageBuilder;

class ContentPage extends PageType
{
    public function __construct(
        public array $blocks = [])
    {}

    public function getBlocks(): Collection
    {
        return collect($this->data['blockData'] ?? [])->map(function ($block) {
            if (! $class = Chord::getBlockTypeClass($block['type'])) {
                throw new \Exception("Block Class for key '{$block['type']}' does not exist");
            }

            return $class::from($block['data']);
        });
    }

    public function getSchema(): array
    {
        return [
            PageBuilder::make('data.blocks')
        ];
    }
}
