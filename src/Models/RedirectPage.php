<?php

namespace LiveSource\Chord\Models;

use LiveSource\Chord\Filament\Actions\EditPageSettingsAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use Parental\HasParent as HasInheritance;

class RedirectPage extends ChordPage
{
    use HasInheritance;

    protected bool $hasContentForm = false;

    public function settingsAction(): ?EditPageSettingsAction
    {
        return parent::settingsAction();
    }

    public function tableRecordURL(): ?string
    {
        return PageResource::getUrl('children', ['parent' => $this->id]);
    }

    public function afterCreateRedirectURL(): ?string
    {
        return null;
    }
}
