<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Actions\EditAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\Page;

class EditPageSettingsAction extends EditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->form(PageResource::settingsFormFields())
            ->successRedirectUrl(function (Page $record, array $arguments): string {
                return PageResource::getUrl('edit', ['record' => $record]);
            });
    }
}
