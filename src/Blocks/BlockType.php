<?php

namespace LiveSource\Chord\Blocks;

use Filament\Forms\Components\Builder\Block as BuilderBlock;
use LiveSource\Chord\Facades\Chord;
use Spatie\LaravelData\Data;

abstract class BlockType extends Data
{
    protected static string $component = '';

    public static function getLabel(): string
    {
        return str((new \ReflectionClass(static::class))->getShortName())->headline()->toString();
    }

    public static function getDefaultKey(): string
    {
        return str((new \ReflectionClass(static::class))->getShortName())->toString();
    }

    public static function getFormSchema(): array
    {
        return [];
    }

    public static function getBuilderBlock(): BuilderBlock
    {
        return BuilderBlock::make(static::getDefaultKey())
            ->label(static::getLabel())
            ->schema(static::getFormSchema());
    }

    public function getComponent(): string
    {
        return Chord::resolveComponent(static::$component);
    }

    public function isLivewireComponent(): bool
    {
        return str_contains($this->getComponent(), 'livewire');
    }
}
