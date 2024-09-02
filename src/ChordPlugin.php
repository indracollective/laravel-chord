<?php

namespace LiveSource\Chord;

use Filament\Contracts\Plugin;
use Filament\Panel;
use LiveSource\Chord\Filament\Resources\PageResource;
use LiveSource\Chord\Http\Middleware\PreviewMiddleware;

class ChordPlugin implements Plugin
{
    public function getId(): string
    {
        return 'chord';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverLivewireComponents(
                dirname(__FILE__) . '/Filament/Resources/PageResource/RelationManagers',
                'LiveSource\\Chord\\Filament\\Resources\\PageResource\\RelationManagers',
            )
            //->discoverResources(dirname(__FILE__) . '/Filament/Resources', 'LiveSource\\Chord\\Filament\\Resources')
            ->resources([PageResource::class])
            ->middleware([PreviewMiddleware::class], isPersistent: true);
    }

    public function boot(Panel $panel): void {}

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
