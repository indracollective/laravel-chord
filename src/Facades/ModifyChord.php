<?php

namespace LiveSource\Chord\Facades;

use Illuminate\Support\Facades\Facade;

class ModifyChord extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \LiveSource\Chord\ModifyChord::class;
    }
}
