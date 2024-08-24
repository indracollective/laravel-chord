<?php

namespace LiveSource\Chord\Blocks;

use Filament\Forms\Components\TextInput;

class CallToAction extends BlockType
{
    protected static string $component = 'chord::site.blocks.call-to-action';

    public function __construct(public string $title, public string $subtitle) {}

    public static function getFormSchema(): array
    {
        return [
            TextInput::make('title')->required(),
            TextInput::make('subtitle')->required(),
        ];
    }
}
