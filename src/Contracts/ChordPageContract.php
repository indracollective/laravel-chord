<?php

namespace LiveSource\Chord\Contracts;

interface ChordPageContract
{
    public static function defaultBaseLayout(string $layout): void;

    public static function defaultLayout(string $layout): void;

    public function getBaseLayout(): string;

    public function getLayout(): string;
}
