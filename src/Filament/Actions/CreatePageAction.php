<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Actions\CreateAction;
use LiveSource\Chord\Filament\Resources\PageResource;

class CreatePageAction extends CreateAction
{
    public string | int | null $parent_id = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Create Page')
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->iconButton()
            ->size('lg')
            ->modalWidth('md')
            ->form(fn () => PageResource::getSettingsFormSchema($this));
    }
}
