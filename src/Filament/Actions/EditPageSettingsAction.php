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
        $this->iconButton()
            ->icon('heroicon-o-cog-6-tooth')
            ->size('lg')
            ->form(fn (ChordPage $record) => PageResource::getSettingsFormSchema($this));
    }
}
