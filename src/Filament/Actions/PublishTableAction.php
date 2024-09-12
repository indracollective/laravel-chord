<?php

namespace LiveSource\Chord\Filament\Actions;

use Filament\Forms\Components\CheckboxList;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use LiveSource\Chord\Contracts\HasHierarchy;
use LiveSource\Chord\Contracts\Publishable;
use LiveSource\Chord\Models\ChordPage;

class PublishTableAction extends Action
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
            ->modalHeading(fn (Model $record) => "Publish '$record->title'")
            ->modalIcon(FilamentIcon::resolve('heroicon-o-arrow-up-tray') ?? 'heroicon-o-arrow-up-tray')
            ->modalIconColor('success')
            ->modalDescription('Are you sure you want to publish this page?')
            ->modalAlignment(Alignment::Center)
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalSubmitActionLabel(__('filament-actions::modal.actions.confirm.label'))
            ->modalWidth(MaxWidth::Medium)
            ->hidden(fn (Publishable $record) => $record->isPublished())
            ->form(function (Publishable $record) {
                if (! $record instanceof HasHierarchy) {
                    return [];
                }
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
            ->action(function (Publishable $record, array $data) {
                if ($record instanceof HasHierarchy && in_array($record->id, $data['recursive'] ?? [])) {
                    $record->publishRecursively();
                } else {
                    $record->publish();
                }

                $this->success();
            })
            ->successNotificationTitle(fn (array $data) => isset($data['recursive']) ?
                    str($this->getModelLabel())->plural().' published successfully' :
                    $this->getModelLabel().' published successfully'
            );
    }
}
