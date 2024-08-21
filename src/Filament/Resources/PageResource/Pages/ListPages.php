<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Actions\Contracts\HasRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LiveSource\Chord\Filament\Actions\CreatePageAction;
use LiveSource\Chord\Filament\Actions\EditPageSettingsTableAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\Page;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected ?string $maxContentWidth = 'full';

    public ?Page $parent = null;

    public function mount(): void
    {
        if (request()->parent) {
            $this->parent = request()->parent;
            $this->heading = $this->parent->title;
        }

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        $parent = $this->parent;

        return [
            CreatePageAction::make()->fillForm(fn (): array => [
                'parent_id' => $this->parent?->id,
            ]),
        ];
    }

    public function table(Table $table): Table
    {
        $table = static::getResource()::table($table);
        $parent = $this->parent;

        $table
            // filter pages by parent if parent is set
            ->modifyQueryUsing(fn (Builder $query): Builder => (
                $parent ? $query->where('parent_id', $parent->id) : $query
            ))
            // allow the PageType to update the url of the table row link
            ->recordUrl(function (Model $record, Table $table): ?string {
                if ($url = $record->getData()->getTableRecordURL()) {
                    return $url;
                }

                // fallback to default (copied from ListRecords)
                foreach (['view', 'edit'] as $action) {
                    $action = $table->getAction($action);

                    if (! $action) {
                        continue;
                    }
                    $action->record($record);

                    if (($actionGroup = $action->getGroup()) instanceof HasRecord) {
                        $actionGroup->record($record);
                    }

                    if ($action->isHidden()) {
                        continue;
                    }

                    if (! $url = $action->getUrl()) {
                        continue;
                    }

                    return $url;
                }

                $resource = static::getResource();

                foreach (['view', 'edit'] as $action) {
                    if (! $resource::hasPage($action)) {
                        continue;
                    }

                    if (! $resource::{'can' . ucfirst($action)}($record)) {
                        continue;
                    }

                    return $resource::getUrl($action, ['record' => $record]);
                }

                return null;
            });

        return $table;
    }

    public function reorderTable(array $order): void
    {
        Page::setNewOrder($order);
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
