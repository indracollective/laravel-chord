<?php

namespace LiveSource\Chord;

use Filament\Contracts\Plugin;
use Filament\Panel;
use LiveSource\Chord\Filament\Resources\PageResource;

class ChordPlugin implements Plugin
{
    public function getId(): string
    {
        return 'chord';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PageResource::class
        ]);
    }

    public function boot(Panel $panel): void
    {

    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
