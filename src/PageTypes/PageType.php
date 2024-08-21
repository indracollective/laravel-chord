<?php

namespace LiveSource\Chord\PageTypes;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Models\Page;
use Spatie\LaravelData\Data;

abstract class PageType extends Data
{
    protected ?Page $record;

    public bool $hasContentTab = true;

    public static function getLabel(): string
    {
        return str((new \ReflectionClass(static::class))->getShortName())->headline()->toString();
    }

    public static function getDefaultKey(): string
    {
        return str((new \ReflectionClass(static::class))->getShortName())->toString();
    }

    public static function getSettingsFormSchema(?Page $record = null): array
    {
        $pageTypes = Chord::getPageTypeOptionsForSelect();

        return [
            Grid::make(['default' => 1])
                ->schema([
                    Select::make('type')
                        ->options($pageTypes)
                        ->default(array_key_first($pageTypes))
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
                        ->required(),
                ]),
        ];
    }

    public function getContentFormSchema(): ?array
    {
        return null;
    }

    public function hasContentFormSchema(): bool
    {
        return ! empty($this->getContentFormSchema());
    }

    public function afterCreateRedirectURL(): ?string
    {
        return PageResource::getUrl('edit', ['record' => $this->getRecord()]);
    }

    public function record(Page $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function getRecord(): ?Page
    {
        return $this->record;
    }

    public function getTableRecordURL(): ?string
    {
        return null;
    }
}
