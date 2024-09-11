<?php

namespace LiveSource\Chord\Filament\Resources\SiteResource\Pages;

use Filament\Resources\Pages\ListRecords;
use LiveSource\Chord\Filament\Resources\SiteResource;

class ListSites extends ListRecords
{
    protected static string $resource = SiteResource::class;

    protected ?string $maxContentWidth = 'full';
}
