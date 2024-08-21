<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Tables\Actions\EditAction;
use LiveSource\Chord\Models\Page;

class EditPageSettingsTableAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'settings';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('')
            ->icon('heroicon-o-cog-6-tooth')
            ->modalHeading('Edit Page Settings')
            ->modalWidth('sm')
            ->form(function (Page $record) {
                return $record->getData()->getSettingsFormSchema();
            });
    }
}
