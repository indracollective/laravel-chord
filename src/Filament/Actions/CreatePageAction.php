<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Actions\CreateAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\ChordPage;

class CreatePageAction extends CreateAction
{
    public string | int | null $parent_id = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('')
            ->icon('heroicon-s-plus-circle')
            ->iconButton()
            ->size('xl')
            ->modalWidth('md')
            ->form(fn (ChordPage $record) => PageResource::getSettingsFormSchema($this));
    }
}
