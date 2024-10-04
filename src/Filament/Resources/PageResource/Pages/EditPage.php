<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LiveSource\Chord\Filament\Actions\EditPageSettingsAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\ChordPage;

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
            Actions\Action::make('versions')
                ->label('History')
                ->url(fn (ChordPage $record) => PageResource::getUrl('versions', ['record' => $record->{$record->getRouteKeyName()}]))
                ->icon('heroicon-o-clock'),
            Actions\Action::make('open')
                ->label('Open')
                ->url(fn (ChordPage $record) => $record->getLink(true))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->iconPosition(IconPosition::After)
                ->openUrlInNewTab()
                ->color('primary'),
            EditPageSettingsAction::make(),
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

    protected function resolveRecord(int|string $key): Model
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

    public function liveSave(): void
    {
        if ($this->getRecord()->isPublished()) {
            $this->saveDraft(false, false);
        } else {
            $this->getRecord()->withoutRevision();
            $this->save(false, false);
        }
        $this->dispatch('page-updated');
    }

    public function saveDraft(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->getRecord()->asDraft();
        $this->save($shouldRedirect, $shouldSendSavedNotification);
    }

    public function publish(): void
    {
        $this->save(false, true);
    }
}
