<?php

namespace LiveSource\Chord\Blocks;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;

class RichContent extends Block
{
    protected static string $component = 'chord::blocks.rich-content';
    public function __construct(public string $title, public string $content)
    {
    }
    public static function getSchema(): array
    {
        return [
            TextInput::make('title')->required(),
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
            Builder::class::make('blocks')
                ->blocks([
                    Builder\Block::make('heading')
                        ->schema([
                            TextInput::make('content')
                                ->label('Heading')
                                ->required(),
                        ])
                ])
        ];
    }
}
