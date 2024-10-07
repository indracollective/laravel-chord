<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Indra\Revisor\Enums\RevisorMode;
use LiveSource\Chord\Filament\Actions\EditPageSettingsTableAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\ChordPage;

class ListPageVersions extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected ?string $maxContentWidth = 'full';

    public ?ChordPage $record = null;

    public function getHeading(): string
    {
        return $this->getRecord()?->title . ' Versions' ?? '?';
    }

    public function getRecord(): ?ChordPage
    {
        if (! $this->record) {
            if ($recordId = request()->record) {
                $this->record = ChordPage::where('id', $recordId)->firstOrFail();
            }
        }

        return $this->record;
    }

    public function table(Table $table): Table
    {
        $parent = $this->getRecord();

        return static::getResource()::versionsTable($table)
            ->modifyQueryUsing(function (Builder $query) use ($parent): Builder {
                return $query->withVersionRecords()->where('record_id', $parent->getKey());
            })->recordUrl(function (Model $record, Table $table): ?string {
                return $this->getResource()::getUrl('version', [
                    'record' => $record->record_id,
                    'version' => $record->id,
                ]);
            });
    }

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        $resourceURL = $resource::getUrl();

        $breadcrumbs = [
            $resourceURL => $resource::getBreadcrumb(),
            //...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if ($parentPage = $this->getRecord()) {
            $breadcrumbs["$resourceURL/{$parentPage->id}"] = $parentPage->title;
        }

        $breadcrumbs[] = 'List';

        return $breadcrumbs;
    }

    public function reorderTable(array $order): void
    {
        ChordPage::setNewOrder($order);
    }

    protected function configureEditAction(EditAction $action): void
    {
        parent::configureEditAction($action);

        // force modal for edit settings action
        if ($action instanceof EditPageSettingsTableAction) {
            $action->url(false);
            $action->successRedirectUrl(false);
        }

    }
}
