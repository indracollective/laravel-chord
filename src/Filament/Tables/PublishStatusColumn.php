<?php

namespace LiveSource\Chord\Filament\Tables;

use Filament\Tables\Columns\TextColumn;
use Indra\Revisor\Contracts\HasRevisor;

class PublishStatusColumn extends TextColumn
{
    public bool $showDraftStatus = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Status')
            ->badge()
            ->getStateUsing(function (HasRevisor $record) {
                if (!$record->is_published) {
                    return 'draft';
                }

                return $record->isRevised() ? 'published,revised' : 'published';
            })
            ->separator(",")
            ->color(fn (string $state): string => match ($state) {
                'revised' => 'warning',
                'published' => 'success',
                'draft' => 'gray',
            });
    }

    public function showDraftStatus(bool $showDraftStatus = true): static
    {
        $this->showDraftStatus = $showDraftStatus;

        return $this;
    }

    public function getShowDraftStatus(): bool
    {
        return $this->showDraftStatus;
    }
}
