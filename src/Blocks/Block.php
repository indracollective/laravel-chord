<?php

namespace LiveSource\Chord\Blocks;

use Filament\Forms\Components\Builder\Block as BuilderBlock;
use Spatie\LaravelData\Data;

abstract class Block extends Data
{
    protected static string $component = '';

    public static function getLabel(): string
    {
        return str((new \ReflectionClass(static::class))->getShortName())->headline()->toString();
    }

    public static function getName(): string
    {
        return static::class;
    }

    public static function getSchema(): array
    {
        return [];
    }

    public static function getBuilderBlock(): BuilderBlock
    {
        return BuilderBlock::make(static::class)
            ->label(static::getLabel())
            ->schema(static::getSchema());
    }

    public function getComponent(): string
    {
        return static::$component ?? '';
    }

    public function isLivewireComponent(): bool
    {
        return str_contains($this->getComponent(), 'livewire');
    }
}
