<?php

namespace LiveSource\Chord;

class ModifyChord
{
    public array $mods = [
        'createPageAction' => [],
        'contentForm' => [],
        'editPageSettingsTableAction' => [],
        'editSettingsAction' => [],
        'childPagesTableAction' => [],
    ];

    public function createPageAction(callable $callback): static
    {
        return $this->mod('createPageAction', $callback);
    }

    public function contentForm(callable $callback): static
    {
        return $this->mod('contentForm', $callback);
    }

    public function editPageSettingsTableAction(callable $callback): static
    {
        return $this->mod('editPageSettingsTableAction', $callback);
    }

    public function editSettingsAction(callable $callback): static
    {
        return $this->mod('editSettingsAction', $callback);
    }

    public function childPagesTableAction(callable $callback): static
    {
        return $this->mod('childPagesTableAction', $callback);
    }

    protected function mod(string $key, callable $callback): static
    {
        $this->mods[$key][] = $callback;

        return $this;
    }

    protected function getMods(string $key): array
    {
        return $this->mods[$key] ?? [];
    }

    public function apply(string $key, ...$args): static
    {
        foreach ($this->getMods($key) as $mod) {
            call_user_func($mod, ...$args);
        }

        return $this;
    }
}
