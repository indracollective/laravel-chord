<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Actions\EditAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\ChordPage;

class EditPageAction extends EditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            //->iconButton()
//            ->size('lg')
            ->icon('heroicon-o-pencil-square')
            ->label('Edit')
            ->url(fn (ChordPage $record) => PageResource::getUrl('edit', ['record' => $record->id]))
            ->hidden(fn (ChordPage $record) => ! $record->hasContentForm());
    }
}
