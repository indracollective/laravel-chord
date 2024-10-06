<?php

namespace LiveSource\Chord\Filament\Resources;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use LiveSource\Chord\Enums\Menu;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Facades\ModifyChord;
use LiveSource\Chord\Filament\Actions\CreateChildPageTableAction;
use LiveSource\Chord\Filament\Actions\EditPageSettingsTableAction;
use LiveSource\Chord\Filament\Actions\EditPageTableAction;
use Livesource\Chord\Filament\Actions\PublishBulkAction;
use LiveSource\Chord\Filament\Actions\PublishTableAction;
use Livesource\Chord\Filament\Actions\UnpublishBulkAction;
use LiveSource\Chord\Filament\Actions\UnpublishTableAction;
use LiveSource\Chord\Filament\Actions\ViewChildPagesTableAction;
use LiveSource\Chord\Filament\Tables\PublishStatusColumn;
use LiveSource\Chord\Models\ChordPage;

class PageResource extends Resource
{
    protected static ?string $model = ChordPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Page';

    protected static ?string $navigationGroup = 'CMS';

    public static function getSettingsFormSchema(): array
    {
        $pageTypes = Chord::getPageTypeOptionsForSelect();

        return [
            Grid::make(['default' => 1])
                ->schema([
                    // todo make this only show folder options
                    SelectTree::make('parent_id')
                        ->placeholder('Top level')
                        ->relationship('parent', 'title', 'parent_id')
                        ->label('Parent')
                        ->generateSlug(),
                    TextInput::make('title')
                        ->required()
                        ->generateSlug()
                        ->autofocus(),
                    TextInput::make('slug')
                        ->required()
                        ->live(onBlur: false)
                        ->afterStateUpdated(function (string $operation, $state, Set $set) {
                            $set('slug', str($state)->slug());
                        })
                        ->unique(modifyRuleUsing: function (Unique $rule, $get, ?Model $record) {
                            return $record ?
                                $rule->where('parent_id', $get('parent_id'))->ignore($get($record->getKeyName())) :
                                $rule;
                        }),
                    Select::make('type')
                        ->options($pageTypes)
                        ->default(array_key_first($pageTypes) ?? null)
                        ->selectablePlaceholder(false)
                        ->required(),
                    CheckboxList::make('show_in_menus')
                        ->options(Menu::class)
                        ->afterStateHydrated(function ($component, $state, $context) {
                            if (! filled($state) && $context === 'create') {
                                $component->state([Menu::Header, Menu::Footer]);
                            }
                        }),
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
        $orderColumn = static::$model::getOrderColumnName();

        return $table
            ->reorderable($orderColumn)
            ->defaultSort($orderColumn)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn (string $state) => str($state)->headline()),
                Tables\Columns\TextColumn::make('path')
                    ->url(fn (ChordPage $record) => $record->getLink(true))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->color('primary')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                PublishStatusColumn::make('publish_status'),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created')
                    ->prefix('By: ')
                    ->description(fn (Model $record) => 'On: ' . $record->created_at)
                    ->placeholder('-')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('editor.name')
                    ->label('Updated')
                    ->prefix('By: ')
                    ->description(fn (Model $record) => 'On: ' . $record->updated_at)
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('publisher.name')
                    ->label('Published')
                    ->prefix('By: ')
                    ->description(fn (Model $record) => 'On: ' . $record->published_at)
                    ->placeholder('-'),
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
                Tables\Actions\ActionGroup::make([
                    EditPageTableAction::make(),
                    EditPageSettingsTableAction::make(),
                    Tables\Actions\Action::make('versions')
                        ->label('History')
                        ->url(fn (ChordPage $record) => PageResource::getUrl('versions', ['record' => $record->{$record->getRouteKeyName()}]))
                        ->icon('heroicon-o-clock'),
                    Tables\Actions\Action::make('view_published')
                        ->label('View Published')
                        ->url(fn (ChordPage $record) => $record->getLink(true))
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->hidden(fn (ChordPage $record) => ! $record->is_published),
                    Tables\Actions\Action::make('view_revision')
                        ->label('View Revision')
                        ->url(fn (ChordPage $record) => $record->getLink(true, $record->id))
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->hidden(fn (ChordPage $record) => $record->isPublished()),
                    Tables\Actions\ActionGroup::make([
                        CreateChildPageTableAction::make(),
                        PublishTableAction::make(),
                        UnpublishTableAction::make(),
                    ])->dropdown(false),
                ]),

                ViewChildPagesTableAction::make('children'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    PublishBulkAction::make(),
                    UnpublishBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function versionsTable(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('version_number')->label('Version'),
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\IconColumn::make('is_current')->boolean(),
            Tables\Columns\IconColumn::make('is_published')->boolean(),
            Tables\Columns\TextColumn::make('creator.name')
                ->label('Created')
                ->prefix('By: ')
                ->description(fn (ChordPage $record) => 'On: ' . $record->created_at),
            Tables\Columns\TextColumn::make('editor.name')
                ->label('Updated')
                ->prefix('By: ')
                ->description(fn (ChordPage $record) => 'On: ' . $record->updated_at),
            Tables\Columns\TextColumn::make('publisher.name')
                ->label('Published')
                ->prefix('By: ')
                ->description(fn (ChordPage $record) => 'On: ' . $record->published_at),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //RevisionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => PageResource\Pages\ListPages::route('/'),
            'children' => PageResource\Pages\ListPages::route('/{parent}'),
            'edit' => PageResource\Pages\EditPage::route('/{record}/edit/{version?}'),
            'versions' => PageResource\Pages\ListPageVersions::route('/{record?}/versions'),
        ];
    }
}
