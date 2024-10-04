<?php

declare(strict_types=1);

namespace LiveSource\Chord\Models;

use LiveSource\Chord\Concerns\HasContentBlocks;
use LiveSource\Chord\Concerns\HasInheritance;

class ContentPage extends ChordPage
{
    use HasContentBlocks;
    use HasInheritance;

    protected static string $defaultLayout = 'chord::pages.content-page';
}
