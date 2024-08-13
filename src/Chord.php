<?php

namespace LiveSource\Chord;

class Chord
{
    protected array $blockTypes = [];
    public function __construct()
    {

    }

    public function getBlockTypes(): array
    {
        return $this->blockTypes;
    }

    public function registerBlockType($type): void
    {
        $this->blockTypes[$type::getName()] = $type;
    }

    public function registerBlockTypes(array $types): void
    {
        foreach ($types as $type) {
            $this->registerBlockType($type);
        }
    }
}
