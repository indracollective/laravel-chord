<?php

namespace LiveSource\Chord\Blocks;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;

class Hero extends BlockType
{
    protected static string $component = 'blocks.hero';

    public function __construct(public string $title, public string $content) {}

    public static function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->refreshesPreview(),

            RichEditor::make('content')
                ->refreshesPreview()
                ->toolbarButtons([
                    'attachFiles',
                    'blockquote',
                    'bold',
                    'bulletList',
                    'codeBlock',
                    'h1',
                    'h2',
                    'h3',
                    'italic',
                    'link',
                    'orderedList',
                    'redo',
                    'strike',
                    'underline',
                    'undo',
                ]),
        ];
    }
}
