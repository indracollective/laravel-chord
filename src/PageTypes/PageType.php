<?php

namespace Livesource\Chord\PageTypes;

use Spatie\LaravelData\Data;

abstract class PageType extends Data
{
    public static function getLabel(): string
    {
        return str((new \ReflectionClass(static::class))->getShortName())->headline()->toString();
    }

    public static function getDefaultKey(): string
    {
        return str((new \ReflectionClass(static::class))->getShortName())->toString();
    }
}
