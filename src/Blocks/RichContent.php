<?php

namespace LiveSource\Chord\Blocks;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use LiveSource\Chord\Filament\Resources\PageResource\Pages\EditPage;

class RichContent extends BlockType
{
    protected static string $component = 'blocks.rich-content';

    public function __construct(public string $title, public string $content) {}

    public static function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->live(onBlur: false)
                ->afterStateUpdated(function (EditPage $livewire) {
                    $livewire->liveSave();
                }),

            RichEditor::make('content')->toolbarButtons([
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
