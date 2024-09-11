<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Forms\Components\CheckboxList;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\Action;
use LiveSource\Chord\Models\ChordPage;

class UnpublishPageTableAction extends Action
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
            ->icon(FilamentIcon::resolve('heroicon-o-arrow-down-tray') ?? 'heroicon-o-arrow-down-tray')
            ->color('warning')
            ->deselectRecordsAfterCompletion()
            ->modalHeading(fn (ChordPage $record) => "Unpublish '$record->title'")
            ->modalIcon(FilamentIcon::resolve('heroicon-o-arrow-down-tray') ?? 'heroicon-o-arrow-down-tray')
            ->modalIconColor('warning')
            ->modalDescription('Are you sure you want to unpublish this page?')
            ->modalAlignment(Alignment::Center)
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalSubmitActionLabel(__('filament-actions::modal.actions.confirm.label'))
            ->modalWidth(MaxWidth::Medium)
            ->hidden(fn (ChordPage $record) => ! $record->isPublished())
            ->form(function (ChordPage $record) {
                $children = $record->children()->published();
                $numChildren = $children->count();
                if ($numChildren === 0) {
                    return [];
                }
                $label = $numChildren === 1 ? '1 child' : "$numChildren children";
                $label = "Unpublish $label of '$record->title'";

                return [
                    CheckboxList::make('recursive')
                        ->label('Would you like to unpublish descendant pages, too?')
                        ->options([$record->id => $label])
                        ->default(fn (ChordPage $record) => [$record->id]),
                ];
            })
            ->action(function (ChordPage $record, array $data) {
                if (in_array($record->id, $data['recursive'] ?? [])) {
                    $record->unpublishRecursively();
                } else {
                    $record->unpublish();
                }
            });
    }
}
