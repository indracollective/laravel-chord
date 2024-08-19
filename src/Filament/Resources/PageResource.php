<?php

namespace LiveSource\Chord\Filament\Resources;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Models\Page;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    //    public static function settingsFormTab(Form $form): Tabs\Tab
    //    {
    //        return Tabs\Tab::make('Settings')->schema([
    //            Fieldset::make('Seo')->schema([
    //                TextInput::make('meta_title'),
    //                TextInput::make('meta_description'),
    //            ]),
    //        ]);
    //    }

    public static function settingsFormFields(?Page $record = null): array
    {
        $pageTypes = Chord::getPageTypeOptionsForSelect();

        return [
            Grid::make(['default' => 1, 'sm' => 2])
                ->schema([
                    Select::make('page_type')
                        ->options($pageTypes)
                        ->default(array_key_first($pageTypes))
                        ->required(),
                    // todo make this only show folder options
                    SelectTree::make('parent_id')
                        ->placeholder('Top level')
                        ->relationship('parent', 'title', 'parent_id')
                        ->label('Parent Folder'),
                    TextInput::make('title')
                        ->required()
                        ->generateSlug(),
                    TextInput::make('slug')
                        ->required(),
                ]),
        ];
    }

    public static function formFields(Form $form): array
    {
        return [
<<<<<<< Updated upstream
            Split::make([
                Section::make('main')
                    ->schema($form->getRecord()->typeObject()->schema()),
                Section::make('preview')
                    ->schema([]),
            ]),
=======

                Grid::make(['default' => 1])
                    ->schema($form->getRecord()->typeObject()->getSchema())
                    ->hiddenLabel(true)
                    ->grow()
                    ->columnSpanFull(),

>>>>>>> Stashed changes
        ];
    }

    public static function form(Form $form): Form
    {
        $form->schema(static::formFields($form));

        $record = $form->getRecord();

        if ($record) {
            $record->configureForm($form);
        }

        //dd($record);
        //        Fieldset::make('blocks-section')
        //            ->schema([PageBuilder::make('blocks')]),
        //dump($form->getFlatComponentsByKey());
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->defaultSort('order_column')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('parent_id'),
                Tables\Columns\TextColumn::make('order_column'),

            ])
            ->actions([
<<<<<<< Updated upstream
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('visual')
                    ->label('Visual edit')
                    ->url(fn (Page $record): string => route('filament.admin.resources.pages.visual', $record)),
=======
                Tables\Actions\EditAction::make()
>>>>>>> Stashed changes
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (EloquentBuilder $query): EloquentBuilder {
                if (request()->has('parent')) {
                    return $query->where('parent_id', request()->get('parent'));
                }

                return $query->where('parent_id', null);
            });
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
            'edit' => PageResource\Pages\EditPage::route('/{record}/edit'),
            'test' => PageResource\Pages\TestPage::route('/test'),
        ];
    }
}
