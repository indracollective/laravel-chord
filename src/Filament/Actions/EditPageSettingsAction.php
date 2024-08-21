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

        $this->form(PageResource::settingsFormFields())
            ->successRedirectUrl(function (ChordPage $record, array $arguments): string {
                return PageResource::getUrl('edit', ['record' => $record]);
            });
    }
}
