<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Tables\Actions\Action;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\ChordPage;

class ViewChildPagesTableAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return '';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->url(fn (ChordPage $record) => PageResource::getUrl('children', ['parent' => $record->id]))
            ->icon('heroicon-o-chevron-right')
            ->label('');
    }
}
