<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Actions\CreateAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\ChordPage;

class CreatePageAction extends CreateAction
{
    public string|int|null $parent_id = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Create Page')
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->iconButton()
            ->size('lg')
            ->modalWidth('md')
            ->form(fn () => PageResource::getSettingsFormSchema($this))
            ->successRedirectUrl(fn (ChordPage $record) => PageResource::getUrl('edit', ['record' => $record->id]))
            ->mutateFormDataUsing(function (array $data): array {
                $data['is_published'] = false;

                return $data;
            });
    }
}
