<?php

namespace LiveSource\Chord\Filament\Resources;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
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

    public static function formTabs(Form $form)
    {
        return Tabs::make('Tabs')
            ->tabs([
                $this->generalFormTab($form),
                $this->seoFormTab($form),
            ]);
    }

    public static function settingsFormTab(Form $form): Tabs
    {
        return Tab::make('Settings')->schema([
            Fieldset::make('General')->schema([
                TextInput::make('title')->required(),
                TextInput::make('slug')->required(),
                SelectTree::make('parent_id')
                    ->relationship('parent', 'title', 'parent_id'),
            ]),
            Fieldset::make('Seo')->schema([
                TextInput::make('meta_title'),
                TextInput::make('meta_description'),
            ]),
        ]);
    }

    public static function contentFormTab(Form $form): Tabs
    {
        $blockTypes = collect(Chord::getBlockTypes())->map(function ($type) {
            return $type::getBuilderBlock();
        })->toArray();

        return Tab::make('Seo')->schema([
            Section::make('blocks-section')
                ->schema([
                    Builder::class::make('blocks')
                        ->blocks($blockTypes),
                ]),
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([static::formTabs($form)]);
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
            'create' => PageResource\Pages\CreatePage::route('/create'),
            'edit' => PageResource\Pages\EditPage::route('/{record}/edit'),
            'visual' => PageResource\Pages\VisualEditPage::route('/{record}/visual'),
            'test' => PageResource\Pages\TestPage::route('/test'),
        ];
    }
}
