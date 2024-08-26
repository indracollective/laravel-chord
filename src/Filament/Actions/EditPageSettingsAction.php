<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Actions\EditAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\ChordPage;

class EditPageSettingsAction extends EditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->icon(fn () => 'heroicon-o-cog-6-tooth')
            ->label('Settings')
            ->form(fn (ChordPage $record) => PageResource::getSettingsFormSchema($this))
            ->recordTitle(fn (ChordPage $record) => $record->title)
            ->modalHeading(fn (ChordPage $record) => 'Configure ' . $record->title);
    }
}
