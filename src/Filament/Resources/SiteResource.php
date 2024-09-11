<?php

namespace LiveSource\Chord\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use LiveSource\Chord\Facades\ModifyChord;
use LiveSource\Chord\Filament\Resources\SiteResource\Pages\ListSites;
use LiveSource\Chord\Models\ChordPage;
use LiveSource\Chord\Models\Site;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $modelLabel = 'Site';

    protected static ?string $recordRouteKeyName = 'uuid';

    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        $form->schema([
            TextInput::make('title')->required(),
        ]);

        ModifyChord::apply('siteForm', $form);

        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'revised' => 'warning',
                        'published' => 'success',
                    }),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created')
                    ->prefix('By: ')
                    ->description(fn (ChordPage $record) => 'On: '.$record->created_at),
                Tables\Columns\TextColumn::make('editor.name')
                    ->label('Updated')
                    ->prefix('By: ')
                    ->description(fn (ChordPage $record) => 'On: '.$record->updated_at),
                Tables\Columns\TextColumn::make('publisher.name')
                    ->label('Published')
                    ->prefix('By: ')
                    ->description(fn (ChordPage $record) => 'On: '.$record->published_at),
            ])
            ->configure()
            ->filters([
            ])
            ->actions([

            ])
            ->bulkActions([

            ]);
    }

    public static function revisionsTable(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('title'),
            Tables\Columns\IconColumn::make('is_published')->boolean(),
            Tables\Columns\IconColumn::make('is_current')->boolean(),
            Tables\Columns\TextColumn::make('creator.name')
                ->label('Created')
                ->prefix('By: ')
                ->description(fn (ChordPage $record) => 'On: '.$record->created_at),
            Tables\Columns\TextColumn::make('editor.name')
                ->label('Updated')
                ->prefix('By: ')
                ->description(fn (ChordPage $record) => 'On: '.$record->updated_at),
            Tables\Columns\TextColumn::make('publisher.name')
                ->label('Published')
                ->prefix('By: ')
                ->description(fn (ChordPage $record) => 'On: '.$record->published_at),
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
            'index' => ListSites::route('/'),
            // 'edit' => SiteResource\Pages\EditSite::route('/{record}/edit/{revision?}'),
            // 'revisions' => SiteResource\Pages\ListRevisions::route('/{record?}/revisions'),
        ];
    }
}
