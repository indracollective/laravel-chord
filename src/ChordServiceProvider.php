<?php

namespace LiveSource\Chord;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\View;
use LiveSource\Chord\Blocks\CallToAction;
use LiveSource\Chord\Blocks\Hero;
use LiveSource\Chord\Blocks\RichContent;
use LiveSource\Chord\Commands\ChordCommand;
use LiveSource\Chord\Facades\Chord as ChordFacade;
use LiveSource\Chord\Models\ContentPage;
use LiveSource\Chord\Models\Folder;
use LiveSource\Chord\Testing\TestsChord;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ChordServiceProvider extends PackageServiceProvider
{
    public static string $name = 'chord';

    public static string $viewNamespace = 'chord';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasRoutes($this->getRoutes())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('livesource/chord');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        $this->app->bind(ModifyChord::class, fn () => new ModifyChord);
    }

    public function packageBooted(): void
    {
        View::composer('*', function ($view) {
            $view->with('pagesForMenu', function (string $menu) {
                return \LiveSource\Chord\Facades\Chord::pagesForMenu($menu);
            });

            $view->with('chordComponent', function (string $component) {
                return \LiveSource\Chord\Facades\Chord::resolveComponent($component);
            });
        });

        ChordFacade::registerPageTypes([
            ContentPage::class,
            Folder::class,
            //            Redirect::class,
        ]);

        ChordFacade::registerBlockTypes([
            RichContent::class,
            CallToAction::class,
            Hero::class,
        ]);

        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        TextInput::macro('generateSlug', function () {
            $this->live(onBlur: true)
                ->afterStateUpdated(function (string $operation, $state, Set $set) {
                    if ($operation !== 'create') {
                        return;
                    }
                    $set('slug', str($state)->slug());
                });

            return $this;
        });

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/chord/{$file->getFilename()}"),
                ], 'chord-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsChord);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'livesource/chord';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('chord', __DIR__ . '/../resources/dist/components/chord.js'),
            Css::make('chord-styles', __DIR__ . '/../resources/dist/chord.css'),
            Js::make('chord-scripts', __DIR__ . '/../resources/dist/chord.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            ChordCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return ['web'];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_chord_table',
        ];
    }
}
