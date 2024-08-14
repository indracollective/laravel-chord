<?php

namespace LiveSource\Chord\Filament\Resources;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Models\Page;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $blockTypes = collect(Chord::getBlockTypes())->map(function ($type) {
            return $type::getBuilderBlock();
        })->toArray();

        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Section::make('Form')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('title')
                                    ->afterStateUpdated(function ($get, $set, ?string $state) {
                                        if (! $get('meta.is_slug_changed_manually') && filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->reactive()
                                    ->live(onBlur: false, debounce: 500)
                                    ->required(),
                                TextInput::make('slug')
                                    ->afterStateUpdated(function ($set) {
                                        $set('meta.is_slug_changed_manually', true);
                                    })
                                    ->reactive()
                                    ->required(),
                                SelectTree::make('parent_id')
                                    ->relationship('parent', 'title', 'parent_id'),
                                Section::make('blocks-section')
                                    ->schema([
                                        Builder::class::make('blocks')
                                            ->blocks($blockTypes),
                                    ]),
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->defaultSort('order_column')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->getStateUsing(function (Page $record) {
                        return self::getNestedPrefix($record->id) . ($record->parent_id ? '--&nbsp;' : '') . $record->title;
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('parent_id'),
                Tables\Columns\TextColumn::make('order_column'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('visual')
                    ->label('Visual edit')
                    ->url(fn (Page $record): string => route('filament.admin.resources.pages.visual', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function getNestedPrefix($parent_id, $prefix = ''): string
    {
        static $parents = null;
        if ($parents == null) {
            $parents = self::$model::all()->pluck('parent_id', 'id');
        }
        if ($parent_id == 0) {
            return '';
        }
        if (isset($parents[$parent_id]) && $parents[$parent_id]) {
            $prefix .= self::getNestedPrefix($parents[$parent_id], $prefix . '&nbsp;&nbsp;');
        }

        return $prefix;
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
            'create' => PageResource\Pages\CreatePage::route('/create'),
            'edit' => PageResource\Pages\EditPage::route('/{record}/edit'),
            'visual' => PageResource\Pages\VisualEditPage::route('/{record}/visual'),
            'test' => PageResource\Pages\TestPage::route('/test'),
        ];
    }
}
