<?php

namespace LiveSource\Chord\Models;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use LiveSource\Chord\Filament\Forms\PageBuilder;
use Parental\HasParent as HasInheritance;

class BlogIndex extends ChordPage
{
    use HasInheritance;

    public function contentForm(Form $form): ?Form
    {
        return $form->schema([
            Grid::make(['default' => 1])
                ->schema([
                    TextInput::make('title'),
                    PageBuilder::make('content.blocks'),
                ]),
        ]);
    }
}
