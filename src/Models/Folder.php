<?php

namespace LiveSource\Chord\Models;

use LiveSource\Chord\Filament\Resources\PageResource;
use Parental\HasParent as HasInheritance;

class Folder extends ChordPage
{
    use HasInheritance;

    protected bool $hasContentForm = false;

    public function tableRecordURL(): ?string
    {
        return PageResource::getUrl('children', ['parent' => $this->id]);
    }

    public function afterCreateRedirectURL(): string
    {
        return PageResource::getUrl('children', ['parent' => $this->id]);
    }
}
