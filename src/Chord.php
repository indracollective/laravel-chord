<?php

namespace LiveSource\Chord;

use Illuminate\Support\Arr;

class Chord
{
    protected array $blockTypes = [];

    protected array $pageTypes = [];

    public function __construct() {}

    public function registerPageType(string $class, ?string $key): void
    {
        $this->pageTypes[$key ?? $class::defaultKey()] = $class;
    }

    public function registerPageTypes(array $types): void
    {
        foreach ($types as $key => $class) {
            $this->registerPageType($class, is_string($key) ? $key : null);
        }
    }

    public function registerBlockType(string $class, ?string $key): void
    {
        $this->blockTypes[$key ?? $class::defaultKey()] = $class;
    }

    public function registerBlockTypes(array $types): void
    {
        foreach ($types as $key => $class) {
            $this->registerBlockType($class, is_string($key) ? $key : null);
        }
    }

    public function getBlockTypes(): array
    {
        return $this->blockTypes;
    }

    public function getBlockTypeClass(string $key): ?string
    {
        return Arr::get($this->blockTypes, $key);
    }

    public function getPageTypeClass(string $key): ?string
    {
        return Arr::get($this->pageTypes, $key);
    }

    public function getPageTypes(): array
    {
        return $this->pageTypes;
    }

    public function getPageTypeOptionsForSelect(): array
    {
        return Arr::mapWithKeys($this->pageTypes, fn ($class, $key) => [$key => $class::label()]);
    }
}
