<?php

namespace Livesource\Chord\PageTypes;

use Livesource\Chord\Data\LinkData;

class Redirect extends PageType
{
    public function __construct(
        public LinkData $link
    ) {}
}
