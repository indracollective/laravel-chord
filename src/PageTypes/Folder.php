<?php

namespace LiveSource\Chord\PageTypes;

use LiveSource\Chord\Filament\Resources\PageResource;

class Folder extends PageType
{
    public bool $hasContentTab = false;

    public function __construct(
    ) {}

    public function getTableRecordURL(): ?string
    {
        return PageResource::getUrl('children', ['parent' => $this->getRecord()->id]);
    }
}
