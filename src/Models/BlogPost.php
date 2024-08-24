<?php

namespace LiveSource\Chord\Models;

use LiveSource\Chord\Concerns\HasContentBlocks;
use Parental\HasParent as HasInheritance;

class BlogPost extends ChordPage
{
    use HasContentBlocks;
    use HasInheritance;
}
