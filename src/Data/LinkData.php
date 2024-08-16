<?php

namespace Livesource\Chord\Data;

use Filament\Forms\Components\Select;
use Livesource\Chord\Enums\LinkType;
use Spatie\LaravelData\Data;

class LinkData extends Data
{
    public function __construct(
        public string $type,
        public string $value,
        public ?string $target = null,
        public ?string $text = null,
    ) {}

    public function getSchema(): array
    {
        return [
            Select::make('type')->options(LinkType::class),
        ];
    }
}
