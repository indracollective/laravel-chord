<?php

namespace LiveSource\Chord\Enums;

use Filament\Support\Contracts\HasLabel;

enum Menu: string implements HasLabel
{
    case Header = 'header';
    case Footer = 'footer';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
