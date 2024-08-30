<?php

namespace LiveSource\Chord\Models;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LiveSource\Chord\Concerns\ManagesPagePaths;
use LiveSource\Chord\Contracts\ChordPageContract;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Filament\Actions\EditPageSettingsAction;
use LiveSource\Chord\Filament\Actions\EditPageSettingsTableAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use Parental\HasChildren as HasInheritors;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ChordPage extends Model implements ChordPageContract, Sortable
{
    use HasInheritors;
    use ManagesPagePaths;
    use SortableTrait;

    protected $table = 'chord_pages';

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
        return PageResource::getUrl('edit', ['record' => $this->id]);
    }

    public function afterCreateRedirectURL(): ?string
    {
        return PageResource::getUrl('edit', ['record' => $this->id]);
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

    public function getLink($absolute = false): string
    {
        $path = $this->path === '/' ? $this->path : "/$this->path";

        return $absolute ? rtrim(config('app.url'), '/') . $path : $path;
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChordPage::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChordPage::class, 'parent_id');
    }

    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    public function getChildTypes(): array
    {
        return Chord::getPageTypes();
    }

    public function buildSortQuery(): Builder
    {
        return static::query()->where('parent_id', $this->parent_id);
    }
}
