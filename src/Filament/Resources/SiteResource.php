<?php

namespace LiveSource\Chord\Filament\Resources;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Facades\ModifyChord;
use LiveSource\Chord\Filament\Actions\PublishTableAction;
use LiveSource\Chord\Filament\Actions\UnPublishTableAction;
use LiveSource\Chord\Filament\Resources\SiteResource\Pages\ListSites;
use LiveSource\Chord\Filament\Tables\PublishStatusColumn;
use LiveSource\Chord\Models\Site;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $modelLabel = 'Site';

    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        $form->schema([
            Tabs::make('site')->tabs([
                Tab::make('Main')->schema([
                    TextInput::make('hostname')
                        ->helperText('The name of the site as it appears in the browser address bar')
                        ->required(),
                    TextInput::make('title')
                        ->label('Site name')
                        ->helperText('Human-readable name for the site.')
                        ->required(),
                    Toggle::make('is_default')
                        ->label('Is default site')
                        ->helperText('If true, this site will handle requests for all other hostnames that do not have a site entry of their own'),
                ]),
                Tab::make('SEO')->schema([

                ]),
                Tab::make('Access')->schema([

                ]),
            ])->columnSpan('full'),
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
            ->configure()
            ->filters([
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    EditAction::make(),
                    Tables\Actions\Action::make('versions')
                        ->label('History')
                        ->url(fn (Site $record) => SiteResource::getUrl('versions', ['record' => $record->getKey()]))
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

    public static function versionsTable(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('title'),
            PublishStatusColumn::make('publish_status'),
            Tables\Columns\TextColumn::make('creator.name')
                ->label('Created')
                ->prefix('By: ')
                ->description(fn (Site $record) => 'On: ' . $record->created_at),
            Tables\Columns\TextColumn::make('editor.name')
                ->label('Updated')
                ->prefix('By: ')
                ->description(fn (Site $record) => 'On: ' . $record->updated_at),
            Tables\Columns\TextColumn::make('publisher.name')
                ->label('Published')
                ->prefix('By: ')
                ->description(fn (Site $record) => 'On: ' . $record->published_at),
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
            'edit' => SiteResource\Pages\EditSite::route('/{record}/edit/{version?}'),
            'versions' => SiteResource\Pages\ListSiteRevisions::route('/{record?}/versions'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        return ! config('chord.multisite-enabled') ?
            static::getUrl('edit', ['record' => Chord::getDefaultSite()->getKey()]) :
            parent::getNavigationUrl();
    }

    public static function getNavigationLabel(): string
    {
        return ! config('chord.multisite-enabled') ?
            'Site Settings' :
            parent::getNavigationLabel();
    }
}
