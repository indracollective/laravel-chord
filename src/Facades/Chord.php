<?php

namespace LiveSource\Chord\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \LiveSource\Chord\Chord
 */
class Chord extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \LiveSource\Chord\Chord::class;
    }
}
