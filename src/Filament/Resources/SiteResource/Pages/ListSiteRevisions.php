<?php

namespace LiveSource\Chord\Filament\Resources\SiteResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LiveSource\Chord\Filament\Resources\SiteResource;
use LiveSource\Chord\Models\Site;

class ListSiteRevisions extends ListRecords
{
    protected static string $resource = SiteResource::class;

    protected ?string $maxContentWidth = 'full';

    public ?Site $record = null;

    public function getHeading(): string
    {
        return $this->getRecord()?->title.' Revisions' ?? '?';
    }

    public function getRecord(): ?Site
    {
        if (! $this->record) {
            if ($currentId = request()->current) {
                $this->record = Site::where('uuid', $currentId)->firstOrFail();
            }
        }

        return $this->record;
    }

    public function table(Table $table): Table
    {
        $parent = $this->getRecord();

        return static::getResource()::revisionsTable($table)
            ->modifyQueryUsing(function (Builder $query) use ($parent): Builder {
                return $query->withDrafts()
                    ->where('uuid', $parent->uuid)
                    ->withoutGlobalScope('onlyCurrentInPreviewMode');
            })->recordUrl(function (Model $record, Table $table): ?string {
                return $this->getResource()::getUrl('edit', ['record' => $record->uuid, 'revision' => $record->id]);
            });
    }
}
