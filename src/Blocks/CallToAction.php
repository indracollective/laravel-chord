<?php

namespace LiveSource\Chord\Blocks;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;

class CallToAction extends Block
{
    protected static string $component = 'chord::blocks.call-to-action';
    public function __construct(public string $title, public string $subtitle)
    {
    }
    public static function getSchema(): array
    {
        return [
            TextInput::make('title')->required(),
            TextInput::make('subtitle')->required(),
        ];
    }
}
