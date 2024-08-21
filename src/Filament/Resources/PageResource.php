<?php

namespace LiveSource\Chord\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use LiveSource\Chord\Filament\Actions\EditPageSettingsTableAction;
use LiveSource\Chord\Models\Page;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getContentFormFields(Form $form): array
    {
        return $form->getRecord()->getData()->getFormSchema();
    }

    public static function form(Form $form): Form
    {
        $record = $form->getRecord();

        return $form->schema($record->getData()->getContentFormSchema());
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
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->hidden(fn (Page $record) => ! $record->getData()->hasContentFormSchema()),
                EditPageSettingsTableAction::make(),
                Tables\Actions\Action::make('Children')
                    ->url(fn (Page $record) => PageResource::getUrl('children', ['parent' => $record->id]))
                    ->icon('heroicon-o-chevron-right')
                    ->label(''),
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
            'children' => PageResource\Pages\ListPages::route('/{parent}'),
            'edit' => PageResource\Pages\EditPage::route('/{record}/edit'),
            'test' => PageResource\Pages\TestPage::route('/test'),
        ];
    }
}
