<?php

namespace Livesource\Chord\Filament\Actions;

use Filament\Forms\Components\CheckboxList;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class UnpublishPageBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'unpublish';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Unpublish')
            ->icon(static::getDefaultIcon())
            ->color('warning')
            ->deselectRecordsAfterCompletion()
            ->modalHeading(fn (Collection $records) => $records->count() === 1 ? 'Unpublish \''.$records->first()->title.'\'' : 'Unpublish pages')
            ->modalIcon(static::getDefaultIcon())
            ->modalIconColor('warning')
            ->modalDescription(fn (Collection $records) => $records->count() === 1 ?
                'Are you sure you want to unpublish this page?' :
                'Are you sure you want to unpublish these pages?'
            )
            ->modalAlignment(Alignment::Center)
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalSubmitActionLabel(__('filament-actions::modal.actions.confirm.label'))
            ->modalWidth(MaxWidth::Medium)
            ->form(function (Collection $records) {
                $withPublishedChildren = $records->mapWithKeys(function ($record) {
                    $children = $record->children()->published();
                    $numChildren = $children->count();
                    if ($numChildren === 0) {
                        return [];
                    }
                    $label = $numChildren === 1 ? '1 child' : "$numChildren children";

                    return [$record->id => "Unpublish $label of '$record->title'"];
                })->filter();

                if ($withPublishedChildren->isEmpty()) {
                    return null;
                }

                return [
                    CheckboxList::make('recursive')
                        ->label('Would you like to unpublish descendant pages, too?')
                        ->options($withPublishedChildren)
                        ->default(array_keys($withPublishedChildren->toArray())),
                ];
            })
            ->action(function (Collection $records, array $data) {
                $records->each(function ($record) use ($data) {
                    if (in_array($record->id, $data['recursive'] ?? [])) {
                        $record->unpublishRecursively();
                    } else {
                        $record->unpublish();
                    }
                });
            });
    }

    public static function getDefaultIcon(): ?string
    {
        return FilamentIcon::resolve('heroicon-o-arrow-down-tray') ?? 'heroicon-o-arrow-down-tray';
    }
}
