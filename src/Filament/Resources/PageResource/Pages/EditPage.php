<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use LiveSource\Chord\Filament\Resources\PageResource;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected static string $view = 'chord::cms.edit-page';

    protected ?string $maxContentWidth = 'full';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
