<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Actions\CreateAction;
use LiveSource\Chord\Models\Page;
use LiveSource\Chord\PageTypes\PageType;

class CreatePageAction extends CreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('')
            ->icon('heroicon-s-plus-circle')
            ->iconButton()
            ->size('xl')
            ->form(PageType::getSettingsFormSchema())
            ->successRedirectUrl(function (Page $record, array $arguments): ?string {
                return $record->getData()->afterCreateRedirectURL();
            });
    }
}
