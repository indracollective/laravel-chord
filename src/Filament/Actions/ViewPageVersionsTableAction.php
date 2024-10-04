<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Tables\Actions\Action;

class ViewPageVersionsTableAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'versions';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalContent(view('filament.pages.actions.advance'));
    }
}
