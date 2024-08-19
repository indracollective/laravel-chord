<?php

namespace Livesource\Chord\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Form;
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

