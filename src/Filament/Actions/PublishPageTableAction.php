<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Tables\Actions\Action;
use LiveSource\Chord\Models\ChordPage;

class PublishPageTableAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'publish';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Publish')
            ->icon('heroicon-o-arrow-up-circle')
            ->color('success')
            ->hidden(fn (ChordPage $record) => $record->isPublished())
            ->action(function (ChordPage $record, array $data) {
                $record->publish();
            });
    }
}
