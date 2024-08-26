<?php

namespace LiveSource\Chord\Services;

class Themes
{
    public function getThemes(): array
    {
        return config('chord.themes');
    }

    public function resolveComponentView(string $component): string
    {
        $candidates = collect($this->getThemes())
            ->map(fn ($theme) => $theme === 'app' ? $component : "$theme::$component")
            ->toArray();

        foreach ($candidates as $candidate) {
            $test = str_contains('::', $candidate) ?
                str_replace('::', '::components.', $candidate) :
                'components.' . $candidate;

            if (view()->exists($test)) {
                return $candidate;
            }
        }

        throw new \Exception("No components for $component exist. Possible candidates were: " . implode(', ', $candidates));
    }
}
