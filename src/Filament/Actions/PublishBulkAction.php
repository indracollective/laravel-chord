<?php

namespace Livesource\Chord\Filament\Actions;

use Filament\Forms\Components\CheckboxList;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class PublishBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'publish';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Publish')
            ->icon(FilamentIcon::resolve('heroicon-o-arrow-up-tray') ?? 'heroicon-o-arrow-up-tray')
            ->color('success')
            ->deselectRecordsAfterCompletion()
            ->modalHeading(fn (Collection $records) => $records->count() === 1 ? 'Publish \''.$records->first()->title.'\'' : 'Publish pages')
            ->modalIcon(FilamentIcon::resolve('heroicon-o-arrow-up-tray') ?? 'heroicon-o-arrow-up-tray')
            ->modalIconColor('success')
            ->modalDescription(fn (Collection $records) => $records->count() === 1 ?
                'Are you sure you want to publish this page?' :
                'Are you sure you want to publish these pages?'
            )
            ->modalAlignment(Alignment::Center)
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalSubmitActionLabel(__('filament-actions::modal.actions.confirm.label'))
            ->modalWidth(MaxWidth::Medium)
            ->form(function (Collection $records) {
                $withUnpublishedChildren = $records->mapWithKeys(function ($record) {
                    $children = $record->children()->current()->onlyDrafts();
                    $numChildren = $children->count();
                    if ($numChildren === 0) {
                        return [];
                    }
                    $label = $numChildren === 1 ? '1 child' : "$numChildren children";

                    return [$record->id => "Publish $label of '$record->title'"];
                })->filter();

                if ($withUnpublishedChildren->isEmpty()) {
                    return null;
                }

                return [
                    CheckboxList::make('recursive')
                        ->label('Would you like to publish descendant pages, too?')
                        ->options($withUnpublishedChildren)
                        ->default(array_keys($withUnpublishedChildren->toArray())),
                ];
            })
            ->action(function (Collection $records, array $data) {
                $records->each(function ($record) use ($data) {
                    if (in_array($record->id, $data['recursive'] ?? [])) {
                        $record->publishRecursively();
                    } else {
                        $record->publish();
                    }
                });

                $this->success();
            })
            ->successNotificationTitle(fn (array $data, Collection $records) => isset($data['recursive']) || $records->count() > 1 ?
                    str($this->getModelLabel())->plural().' published successfully' :
                    $this->getModelLabel().' published successfully'
            );
    }
}
