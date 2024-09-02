<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Tables\Actions\Action;
use LiveSource\Chord\Models\ChordPage;

class UnpublishPageTableAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'unpublish';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Unpublish')
            ->icon('heroicon-o-arrow-down-circle')
            ->color('danger')
            ->hidden(fn (ChordPage $record) => ! $record->isPublished())
            ->action(function (ChordPage $record, array $data) {
                $record->withoutRevision()->update(['is_published' => false]);
            });
    }
}
