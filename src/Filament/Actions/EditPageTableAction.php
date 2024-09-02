<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Tables\Actions\EditAction;

class EditPageTableAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return 'edit';
    }
}
