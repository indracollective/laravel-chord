<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Actions\CreateAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\Page;

class CreatePageAction extends CreateAction
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
