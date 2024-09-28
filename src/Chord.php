<?php

namespace LiveSource\Chord;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use LiveSource\Chord\Models\Site;

class Chord
{
    protected ?Site $defaultSite = null;

    protected array $blockTypes = [];

    protected array $pageTypes = [];

    protected array $modifyCreatePageActionUsing = [];

    protected array $modifyContentFormUsing = [];

    protected array $modifySettingsTableActionUsing = [];

    protected array $modifySettingsActionUsing = [];

    protected array $modifyChildPagesTableActionUsing = [];

    public function __construct() {}

    public function registerPageType(string $class, ?string $key): void
    {
        $this->pageTypes[$key ?? $class::defaultKey()] = $class;
    }

    public function registerPageTypes(array $types): void
    {
        foreach ($types as $key => $class) {
            $this->registerPageType($class, is_string($key) ? $key : null);
        }
    }

    public function registerBlockType(string $class, ?string $key): void
    {
        $this->blockTypes[$key ?? $class::getDefaultKey()] = $class;
    }

    public function registerBlockTypes(array $types): void
    {
        foreach ($types as $key => $class) {
            $this->registerBlockType($class, is_string($key) ? $key : null);
        }
    }

    public function getBlockTypes(): array
    {
        return $this->blockTypes;
    }

    public function getBlockTypeClass(string $key): ?string
    {
        return Arr::get($this->blockTypes, $key);
    }

    public function getPageTypeClass(string $key): ?string
    {
        return Arr::get($this->pageTypes, $key);
    }

    public function getPageTypes(): array
    {
        return $this->pageTypes;
    }

    public function getPageTypeOptionsForSelect(): array
    {
        return Arr::mapWithKeys($this->pageTypes, fn ($class, $key) => [$key => $class::label()]);
    }

    public function getDefaultPageType(): ?string
    {
        return config('chord.default_page_type', array_keys($this->getPageTypeOptionsForSelect())[0] ?? null);
    }

    public function getThemes(): array
    {
        return config('chord.themes');
    }

    public function resolveComponent(string $component): string
    {
        $candidates = collect($this->getThemes())
            ->map(fn ($theme) => $theme === 'app' ? $component : "$theme::$component")
            ->toArray();

        foreach ($candidates as $candidate) {
            $test = str_contains($candidate, '::') ?
                str_replace('::', '::components.', $candidate) :
                'components.'.$candidate;

            if (view()->exists($test)) {
                return $candidate;
            }

            if (view()->exists($candidate)) {
                dd("found $candidate");

                return $candidate;
            }

            if (view()->exists($test)) {
                dd("found $test");

                return $candidate;
            }
        }

        throw new \Exception("No components for $component exist. Possible candidates were: ".implode(', ', $candidates));
    }

    public function pagesForMenu(string $menu): Collection
    {
        $pageClass = $this->getBasePageClass();
        $pages = $pageClass::where('parent_id', null)
            ->whereRaw("show_in_menus LIKE '%$menu%'")
            ->orderBy($pageClass::getOrderColumnName())
            ->with(['children' => function ($query) use ($pageClass, $menu) {
                $query->whereRaw("show_in_menus LIKE '%$menu%'")
                    ->orderBy($pageClass::getOrderColumnName())
                    ->with(['children' => function ($query) use ($pageClass, $menu) {
                        $query->whereRaw("show_in_menus LIKE '%$menu%'")
                            ->orderBy($pageClass::getOrderColumnName());
                    }]);
            }])
            ->get();

        return $pages;
    }

    public function getBasePageClass(): string
    {
        return config('chord.base_page_class');
    }

    public function getDefaultSite(): ?Site
    {
        if (! $this->defaultSite) {
            $this->setDefaultSite();
        }

        return $this->defaultSite;
    }

    public function setDefaultSite(): void
    {
        $this->defaultSite = Site::firstWhere('is_default', 1);
    }
}
