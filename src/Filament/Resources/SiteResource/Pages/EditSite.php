<?php

namespace LiveSource\Chord\Filament\Resources\SiteResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LiveSource\Chord\Filament\Resources\SiteResource;
use LiveSource\Chord\Models\Site;

class EditSite extends EditRecord
{
    protected static string $resource = SiteResource::class;
}
