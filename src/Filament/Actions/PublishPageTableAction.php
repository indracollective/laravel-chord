<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Forms\Components\CheckboxList;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\Action;
use LiveSource\Chord\Models\ChordPage;

class PublishPageTableAction extends Action
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
            ->modalHeading(fn (ChordPage $record) => "Publish '$record->title'")
            ->modalIcon(FilamentIcon::resolve('heroicon-o-arrow-up-tray') ?? 'heroicon-o-arrow-up-tray')
            ->modalIconColor('success')
            ->modalDescription('Are you sure you want to publish this page?')
            ->modalAlignment(Alignment::Center)
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalSubmitActionLabel(__('filament-actions::modal.actions.confirm.label'))
            ->modalWidth(MaxWidth::Medium)
            ->hidden(fn (ChordPage $record) => $record->isPublished())
            ->form(function (ChordPage $record) {
                $children = $record->children()->current()->onlyDrafts();
                $numChildren = $children->count();
                if ($numChildren === 0) {
                    return [];
                }
                $label = $numChildren === 1 ? '1 child' : "$numChildren children";
                $label = "Publish $label of '$record->title'";

                return [
                    CheckboxList::make('recursive')
                        ->label('Would you like to publish descendant pages, too?')
                        ->options([$record->id => $label])
                        ->default(fn (ChordPage $record) => [$record->id]),
                ];
            })
            ->action(function (ChordPage $record, array $data) {
                if (in_array($record->id, $data['recursive'] ?? [])) {
                    $record->publishRecursively();
                } else {
                    $record->publish();
                }
            });
    }
}
