<?php

namespace LiveSource\Chord\PageTypes;

use LiveSource\Chord\Data\LinkData;
use LiveSource\Chord\Filament\Forms\PageBuilder;

class Redirect extends PageType
{
    public function __construct(
        public LinkData $to
    ) {}

    public function getFormSchema(): array
    {
        return [
            PageBuilder::make('page_data.to'),
        ];
    }
}
