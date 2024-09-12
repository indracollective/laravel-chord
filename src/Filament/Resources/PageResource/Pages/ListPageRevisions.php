<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Actions\Contracts\HasRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LiveSource\Chord\Filament\Actions\EditPageSettingsTableAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\ChordPage;

class ListPageRevisions extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected ?string $maxContentWidth = 'full';

    public ?ChordPage $record = null;

    public function getHeading(): string
    {
        return $this->getRecord()?->title.' Revisions' ?? '?';
    }

    public function getRecord(): ?ChordPage
    {
        if (! $this->record) {
            if ($currentId = request()->current) {
                $this->record = ChordPage::where('uuid', $currentId)->firstOrFail();
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

    //    protected function getHeaderActions(): array
    //    {
    //        $parent = $this->getParentPage();
    //        $pageTypes = Chord::getPageTypeOptionsForSelect();
    //
    //        $actions = [];
    //
    //        if ($this->getParentPage()) {
    //            $actions[] = Action::make('up')
    //                ->iconbutton()
    //                ->icon('heroicon-o-arrow-turn-left-up')
    //                ->url(function () {
    //                    if ($this->getParentPage()->parent_id) {
    //                        return $this->getResource()::getUrl('children', ['parent' => $this->getParentPage()->parent_id]);
    //                    } else {
    //                        return $this->getResource()::getUrl('index');
    //                    }
    //                })
    //                ->color('gray');
    //        }
    //
    //        $actions[] = CreatePageAction::make()->fillForm(fn (): array => [
    //            'type' => array_key_first($pageTypes),
    //            'parent_id' => $this->getParentPage()?->id,
    //        ]);
    //
    //        if ($this->getParentPage()) {
    //            $actions[] = ActionGroup::make([
    //                EditPageAction::make()->record($this->getParentPage())->label('Edit'),
    //                // todo not sure why this keeps reverting to pencil icon...
    //                EditPageSettingsAction::make()->record($this->getParentPage())->label('Configure'),
    //            ]);
    //        }
    //
    //        return $actions;
    //    }

    //    public function getBreadcrumbs(): array
    //    {
    //        $resource = static::getResource();
    //        $resourceURL = $resource::getUrl();
    //
    //        $breadcrumbs = [
    //            $resourceURL => $resource::getBreadcrumb(),
    //            //...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
    //        ];
    //
    //        if ($parentPage = $this->getParentPage()) {
    //            $breadcrumbs["$resourceURL/{$parentPage->id}"] = $parentPage->title;
    //        }
    //
    //        $breadcrumbs[] = 'List';
    //
    //        return $breadcrumbs;
    //    }

    public function revisionsTable(Table $table): Table
    {
        $table = static::getResource()::table($table);
        $parent = $this->getRecord();

        $table
            ->modifyQueryUsing(function (Builder $query) use ($parent): Builder {
                $query->current();
                // if search is set and parent is set, redirect to the index page with the search
                $search = $this->getTableSearch();
                if ($search && $parent?->id) {
                    $this->redirect($this->getResource()::getURL('index')."?tableSearch=$search");

                    return $query;
                }

                // limit the query to the current parent or top level
                if ($parent) {
                    $query->where('parent_id', $parent->id);
                } elseif (! $search) {
                    $query->where('parent_id', null);
                }

                return $query;
            })
            // allow the PageType to update the url of the table row link
            ->recordUrl(function (Model $record, Table $table): ?string {
                if ($url = $record->tableRecordURL($table)) {
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

                    if (! $resource::{'can'.ucfirst($action)}($record)) {
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
