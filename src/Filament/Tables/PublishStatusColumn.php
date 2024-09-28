<?php

namespace LiveSource\Chord\Filament\Tables;

use Filament\Tables\Columns\TextColumn;

class PublishStatusColumn extends TextColumn
{
    public bool $showCurrentStatus = false;

    public function getState(): mixed
    {
        return $this->getRecord()->getPublishStatuses(true);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Status')
            ->badge()
            ->color(fn (string $state): string => match ($this->getState()) {
                'revised' => 'warning',
                'published' => 'success',
                'current' => 'gray'
            })
            ->separator(', ');
    }

    public function showCurrentStatus(bool $showCurrentStatus = true): static
    {
        $this->showCurrentStatus = $showCurrentStatus;

        return $this;
    }

    public function getShowCurrentStatus(): bool
    {
        return $this->showCurrentStatus;
    }
}
