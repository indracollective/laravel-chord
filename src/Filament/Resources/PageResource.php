<?php

namespace LiveSource\Chord\Filament\Resources;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Facades\ModifyChord;
use LiveSource\Chord\Filament\Actions\EditPageSettingsTableAction;
use LiveSource\Chord\Filament\Actions\EditPageTableAction;
use LiveSource\Chord\Filament\Actions\ViewChildPagesTableAction;
use LiveSource\Chord\Models\ChordPage;

class PageResource extends Resource
{
    protected static ?string $model = ChordPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Page';

    public static function getSettingsFormSchema(): array
    {
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
                    TextInput::make('path'),
                ]),
        ];
    }

    public static function form(Form $form): Form
    {
        $form->schema([
            Grid::make(['default' => 1])
                ->key('main')
                ->schema([
                    TextInput::make('title')->required(),
                ]),
        ]);

        ModifyChord::apply('contentForm', $form);

        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->defaultSort('order_column')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')->formatStateUsing(fn (string $state) => str($state)->headline()),
                Tables\Columns\TextColumn::make('path'),
            ])
            ->emptyStateHeading(function (Table $table) {
                if ($table->hasSearch()) {

                    return 'No pages found for search';
                }

                return 'No pages';
            })
            ->configure()
            ->filters([
            ])
            ->actions([
                EditPageTableAction::make('edit'),
                EditPageSettingsTableAction::make('settings'),
                ViewChildPagesTableAction::make('children'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => PageResource\Pages\ListPages::route('/'),
            'children' => PageResource\Pages\ListPages::route('/{parent}'),
            'edit' => PageResource\Pages\EditPage::route('/{record}/edit'),
            'test' => PageResource\Pages\TestPage::route('/test'),
        ];
    }
}
