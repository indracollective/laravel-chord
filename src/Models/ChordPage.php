<?php

namespace LiveSource\Chord\Models;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Indra\Revisor\Concerns\HasRevisor;
use Indra\Revisor\Contracts\HasRevisor as HasRevisorContract;
use LiveSource\Chord\Concerns\HasHierarchy;
use LiveSource\Chord\Concerns\HasInheritors;
use LiveSource\Chord\Concerns\HasSite;
use LiveSource\Chord\Concerns\HasSortableDrafts;
use LiveSource\Chord\Concerns\ManagesPagePaths;
use LiveSource\Chord\Contracts\ChordPageContract;
use LiveSource\Chord\Contracts\HasHierarchy as HasHierarchyContract;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Filament\Actions\EditPageSettingsAction;
use LiveSource\Chord\Filament\Actions\EditPageSettingsTableAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use Spatie\EloquentSortable\Sortable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Wildside\Userstamps\Userstamps;

class ChordPage extends Model implements ChordPageContract, HasHierarchyContract, Sortable, HasRevisorContract
{
    use HasInheritors;
    use HasSite;
    use HasSlug;
    use HasSortableDrafts;
    use ManagesPagePaths;
    use HasHierarchy;
    use Userstamps;
    use HasRevisor;

    protected string $baseTable = 'pages';

    protected bool $hasContentForm = true;

    protected $fillable = [
        'title',
        'slug',
        'path',
        'content',
        'meta',
        'parent_id',
        'order_column',
        'type',
        'show_in_menus',
        'is_published',
        'created_by',
        'updated_by',
        'deleted_by',
        'site_id',
    ];

    protected $casts = [
        'content' => 'array',
        'meta' => 'array',
        'show_in_menus' => 'array',
    ];

    protected static string $defaultBaseLayout = 'pages.layout';

    protected static string $defaultLayout = '';

    public static function defaultBaseLayout(string $layout): void
    {
        static::$defaultBaseLayout = $layout;
    }

    public static function defaultLayout(string $layout): void
    {
        static::$defaultLayout = $layout;
    }

    public function getBaseLayout(): string
    {
        return $this->meta['baseLayout'] ?? static::$defaultBaseLayout;
    }

    public function getLayout(): string
    {
        return $this->meta['layout'] ?? static::$defaultLayout;
    }

    public function contentForm(Form $form): ?Form
    {
        return $form->schema([
            TextInput::make('title'),
        ]);
    }

    public function tableRecordURL(): ?string
    {
        return PageResource::getUrl('edit', ['record' => $this->{$this->getKeyName()}]);
    }

    public function afterCreateRedirectURL(): ?string
    {
        return PageResource::getUrl('edit', ['record' => $this->{$this->getKeyName()}]);
    }

    public function settingsAction(): ?EditPageSettingsAction
    {
        return EditPageSettingsAction::make();
    }

    public function settingsTableAction(): ?EditPageSettingsTableAction
    {
        return EditPageSettingsTableAction::make();
    }

    public function hasContentForm(): bool
    {
        return $this->hasContentForm;
    }

    public function getLink(bool $absolute = false, ?int $revision = null): string
    {
        $path = $this->path === '/' ? $this->path : "/$this->path";
        $link = $absolute ? rtrim(config('app.url'), '/').$path : $path;

        return $revision ? "$link?revision=$revision" : $link;
    }

    public function isActive(): bool
    {
        return $this->path === request()->path();
    }

    public function isSection(): bool
    {
        return ! $this->isActive() && str_starts_with(request()->path(), $this->path);
    }

    public static function label(): string
    {
        return str((new \ReflectionClass(static::class))->getShortName())->headline()->toString();
    }

    public static function defaultKey(): string
    {
        return str((new \ReflectionClass(static::class))->getShortName())->toString();
    }

    public function buildSortQuery(): Builder
    {
        return static::query()->where('parent_id', $this->parent_id);
    }

    public static function getOrderColumnName(): string
    {
        return (new static)->determineOrderColumnName();
    }

    public function getChildTypes(): array
    {
        return Chord::getPageTypes();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->preventOverwrite()
            ->extraScope(fn ($builder) => $builder
                ->where('parent_id', $this->parent_id)
                ->whereNot('id', $this->id)
            );
    }
}
