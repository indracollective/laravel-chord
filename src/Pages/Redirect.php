<?php

namespace Livesource\Chord\Pages;

use Livesource\Chord\Data\LinkData;

class Redirect extends PageType
{
    public function __construct(
        public LinkData $link
    ) {}
}
