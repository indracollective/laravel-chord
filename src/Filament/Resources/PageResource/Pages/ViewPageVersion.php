<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Indra\Revisor\Contracts\HasRevisor;
use Indra\Revisor\Enums\RevisorMode;
use LiveSource\Chord\Filament\Actions\EditPageSettingsAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\ChordPage;

class ViewPageVersion extends ViewRecord
{
    protected static string $resource = PageResource::class;

    //protected static string $view = 'chord::filament.edit-page';

    //protected ?string $maxContentWidth = 'full';

    //    protected function getHeaderActions(): array
    //    {
    //        return [
    //            Actions\DeleteAction::make(),
    //            Actions\Action::make('versions')
    //                ->label(fn (HasRevisor $record) => 'History (' . $record->versionRecords()->count() . ')')
    //                ->url(fn (ChordPage $record) => PageResource::getUrl('versions', ['record' => $record->{$record->getRouteKeyName()}]))
    //                ->icon('heroicon-o-clock'),
    //            Actions\Action::make('open')
    //                ->label('Open')
    //                ->url(fn (ChordPage $record) => $record->getLink(true))
    //                ->icon('heroicon-o-arrow-top-right-on-square')
    //                ->iconPosition(IconPosition::After)
    //                ->openUrlInNewTab()
    //                ->color('primary'),
    //            EditPageSettingsAction::make(),
    //        ];
    //    }

    //    protected function getFormActions(): array
    //    {
    //        return [
    ////            Actions\Action::make('save')->action('save'),
    ////            Actions\Action::make('publish')->action('publish'),
    ////            $this->getCancelFormAction(),
    //        ];
    //    }

    protected function resolveRecord(int | string $key): Model
    {
        $record = app(static::getModel())->setRevisorMode(RevisorMode::Version);
        $query = $this->getResource()::getEloquentQuery();
        $query->getQuery()->from = $record->getTable();
        $record = $record->find(request()->version);

        if ($record === null) {
            throw (new ModelNotFoundException)->setModel($this->getModel(), [$key]);
        }

        return $record;
    }
}
