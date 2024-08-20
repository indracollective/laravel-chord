<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use LiveSource\Chord\Filament\Resources\PageResource;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected static string $view = 'chord::filament.edit-page';

    protected ?string $maxContentWidth = 'full';

    public function getExtraBodyAttributes(): array
    {
        return [
            'class' => 'chord-edit-page',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
