<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
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
            Actions\CreateAction::make(),
        ];
    }

    public function reorderTable(array $order): void
    {
        Page::setNewOrder($order);
    }
}
