<?php

namespace Livesource\Chord\Enums;

use Filament\Support\Contracts\HasLabel;

enum LinkType: string implements HasLabel
{
    case Url = 'url';
    case Email = 'email';
    case Phone = 'phone';
    case Page = 'page';
    case Media = 'media';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
