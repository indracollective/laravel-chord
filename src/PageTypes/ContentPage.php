<?php

namespace LiveSource\Chord\PageTypes;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;
use LiveSource\Chord\Blocks\BlockType;
use LiveSource\Chord\Chord;
use LiveSource\Chord\Filament\Forms\PageBuilder;

class ContentPage extends PageType
{
    public function __construct(
        public array $blocks = []
    ) {}

    public function getBlocks(): Collection
    {
        return collect($this->blocks)->map(function ($block) {
            /** @var BlockType $class */
            $class = Chord::getBlockTypeClass($block['type']);

            if (! $class) {
                throw new \Exception("Block Class for key '{$block['type']}' does not exist");
            }

            return $class::from($block['data']);
        });
    }

    public function getFormSchema(): array
    {
        return [
            Tabs::make('Main')
                ->contained(false)
                ->tabs([
                    Tabs\Tab::make('Content')->schema([
                        TextInput::make('title'),
                        PageBuilder::make('page_data.blocks'),
                    ]),
                ])->maxWidth('full')->columnSpanFull(),
        ];
    }
}
