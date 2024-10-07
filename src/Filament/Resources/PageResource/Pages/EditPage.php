<?php

namespace LiveSource\Chord\Filament\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Indra\Revisor\Contracts\HasRevisor;
use Indra\Revisor\Enums\RevisorMode;
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
                ->label(fn (HasRevisor $record) => 'History (' . $record->versionRecords()->count() . ')')
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
            Actions\Action::make('save')->action('save'),
            Actions\Action::make('publish')->action('publish'),
            $this->getCancelFormAction(),
        ];
    }

    public function liveSave(): void
    {
        $record = $this->getRecord();

        // if the record is publish and not yet revised, save a new version
        // otherwise, save the changes to the current version
        if (($record->isPublished() && ! $record->isRevised())) {
            $record->saveNewVersionOnSaved(true);
        } else {
            $record->saveNewVersionOnSaved(false);
        }

        parent::save(false, false);

        $this->dispatch('page-updated');
     }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->getRecord()->saveNewVersionOnSaved(true);
        parent::save(false, true);
    }

    public function publish(): void
    {
        $this->getRecord()->publish();

        Notification::make()
            ->success()
            ->title('Page published')
            ->send();
    }
}
