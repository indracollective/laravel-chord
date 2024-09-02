<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LiveSource\Chord\Filament\Resources\PageResource;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected static string $view = 'chord::filament.edit-page';

    protected ?string $maxContentWidth = 'full';

    public function getExtraBodyAttributes(): array
    {
        return [
            'class' => 'chord-edit-page',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('saveDraft')->action('saveDraft'),
            Actions\Action::make('publish')->action('publish'),
            $this->getCancelFormAction(),
        ];
    }

    protected function resolveRecord(int | string $key): Model
    {
        $revision = request()->revision;
        if (! $revision) {
            return parent::resolveRecord($key);
        }

        $record = app(static::getModel())
            ->resolveRouteBindingQuery(
                $this->getResource()::getEloquentQuery(),
                $key,
                $this->getResource()::getRecordRouteKeyName()
            )
            ->withDrafts()
            ->withoutGlobalScope('onlyCurrentInPreviewMode')
            ->firstWhere('id', $revision);

        if ($record === null) {
            throw (new ModelNotFoundException)->setModel($this->getModel(), [$key]);
        }

        return $record;
    }

    public function saveDraft(): void
    {
        $this->getRecord()->asDraft();
        $this->save(false, true);
    }

    public function publish(): void
    {
        $this->save(false, true);
    }
}
