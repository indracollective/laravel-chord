<?php

namespace LiveSource\Chord\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use LiveSource\Chord\Facades\ModifyChord;
use LiveSource\Chord\Filament\Actions\PublishTableAction;
use LiveSource\Chord\Filament\Actions\UnPublishTableAction;
use LiveSource\Chord\Filament\Resources\SiteResource\Pages\ListSites;
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
        return $table->columns([
            Tables\Columns\TextColumn::make('id')
                ->toggleable()
                ->toggledHiddenByDefault(),
            Tables\Columns\TextColumn::make('title')
                ->searchable(),
            Tables\Columns\TextColumn::make('hostname')
                ->searchable(),
            Tables\Columns\IconColumn::make('is_default')
                ->boolean(),
            Tables\Columns\TextColumn::make('publish_statuses_string')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'draft' => 'gray',
                    'revised' => 'warning',
                    'published' => 'success',
                })
                ->separator(', '),
            Tables\Columns\TextColumn::make('creator.name')
                ->label('Created')
                ->prefix('By: ')
                ->description(fn (Model $record) => 'On: '.$record->created_at)
                ->placeholder('-')
                ->toggleable()
                ->toggledHiddenByDefault(),
            Tables\Columns\TextColumn::make('editor.name')
                ->label('Updated')
                ->prefix('By: ')
                ->description(fn (Model $record) => 'On: '.$record->updated_at)
                ->placeholder('-'),
            Tables\Columns\TextColumn::make('publisher.name')
                ->label('Published')
                ->prefix('By: ')
                ->description(fn (Model $record) => 'On: '.$record->published_at)
                ->placeholder('-'),
        ])
            ->configure()
            ->filters([
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    EditAction::make(),
                    Tables\Actions\Action::make('revisions')
                        ->label('History')
                        ->url(fn (Site $record) => SiteResource::getUrl('revisions', ['record' => $record->uuid]))
                        ->icon('heroicon-o-clock'),
                    Tables\Actions\Action::make('view_published')
                        ->label('View Published')
                        ->url(fn (Site $record) => $record->getLink(true))
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->hidden(fn (Site $record) => ! $record->hasPublishedVersion()),
                    Tables\Actions\Action::make('view_revision')
                        ->label('View Revision')
                        ->url(fn (Site $record) => $record->getLink(true, $record->id))
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->hidden(fn (Site $record) => $record->isPublished()),
                    Tables\Actions\ActionGroup::make([
                        PublishTableAction::make(),
                        UnPublishTableAction::make(),
                    ])->dropdown(false),
                ]),
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
                ->description(fn (Site $record) => 'On: '.$record->created_at),
            Tables\Columns\TextColumn::make('editor.name')
                ->label('Updated')
                ->prefix('By: ')
                ->description(fn (Site $record) => 'On: '.$record->updated_at),
            Tables\Columns\TextColumn::make('publisher.name')
                ->label('Published')
                ->prefix('By: ')
                ->description(fn (Site $record) => 'On: '.$record->published_at),
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
            'edit' => SiteResource\Pages\EditSite::route('/{record}/edit/{revision?}'),
            'revisions' => SiteResource\Pages\ListSiteRevisions::route('/{record?}/revisions'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        if (! config('chord.multisite-enabled')) {
            return 'abc';
        }

        return parent::getNavigationUrl();
    }
}
