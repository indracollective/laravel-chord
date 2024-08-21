<?php

namespace LiveSource\Chord\Filament\Actions;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\EditAction;
use Illuminate\Validation\Rules\Unique;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Models\ChordPage;

class EditPageSettingsTableAction extends EditAction
{
    public static function getDefaultName(): ?string
    {
        return '';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('')
            ->icon('heroicon-o-cog-6-tooth')
            ->modalHeading('Edit Page Settings')
            ->modalWidth('sm')
            ->hidden(fn (ChordPage $record) => ! $record->hasContentForm())
            ->form(function (ChordPage $record) {
                $pageTypes = Chord::getPageTypeOptionsForSelect();

                return [
                    Grid::make(['default' => 1])
                        ->schema([
                            Select::make('type')
                                ->options($pageTypes)
                                ->default(array_key_first($pageTypes) ?? null)
                                ->selectablePlaceholder(false)
                                ->required(),
                            // todo make this only show folder options
                            SelectTree::make('parent_id')
                                ->placeholder('Top level')
                                ->relationship('parent', 'title', 'parent_id')
                                ->label('Parent'),
                            TextInput::make('title')
                                ->required()
                                ->generateSlug(),
                            TextInput::make('slug')
                                ->required()
                                ->unique(modifyRuleUsing: function (Unique $rule, $get) {
                                    return $rule->where('parent_id', $get('parent_id'))->ignore($get('id'));
                                }),
                        ]),
                ];
            });
    }
}
