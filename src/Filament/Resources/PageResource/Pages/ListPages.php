<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ListRecords;
use Livesource\Chord\Filament\Actions\CreatePageAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\Page;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected ?string $maxContentWidth = 'full';

    protected ?Page $parent;

    protected function getHeaderActions(): array
    {
        return [
            CreatePageAction::make(),
        ];
    }

    public function reorderTable(array $order): void
    {
        Page::setNewOrder($order);
    }
}
