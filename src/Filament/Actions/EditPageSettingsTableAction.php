<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Tables\Actions\EditAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\ChordPage;

class EditPageSettingsTableAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return '';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('')
            ->icon('heroicon-o-cog-6-tooth')
            ->modalHeading('Edit Page Settings')
            ->modalWidth('sm')
            ->hidden(fn (ChordPage $record) => ! $record->hasContentForm())
            ->form(fn (ChordPage $record) => PageResource::getSettingsFormSchema($this));

    }
}
