<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Tables\Actions\EditAction;

class EditPageTableAction extends EditAction
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->label('');
    }
}
