<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Tables\Actions\Action;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\ChordPage;

class ViewPageRevisionsTableAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'revisions';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalContent(view('filament.pages.actions.advance'))
    }
}
