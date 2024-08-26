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

    protected static string $defaultBaseLayout = 'site.page.default-layout';

    protected static string $defaultLayout = 'site.page.index';

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
        return $this->findView('baseLayout');
    }

    public function getLayout(): string
    {
        return $this->findView('layout');
    }

    public function findView(string $for): string
    {
        $view = match ($for) {
            'baseLayout' => $this->meta['baseLayout'] ?? static::$defaultBaseLayout,
            'layout' => $this->meta['layout'] ?? static::$defaultLayout,
        };

        $candidates = collect(config('chord.themes'))
            ->map(fn ($theme) => $theme === 'app' ? $view : "$theme::$view")
            ->toArray();

        foreach ($candidates as $candidate) {
            $test = str_contains('::', $candidate) ?
                str_replace('::', '::components.', $candidate) :
                'components.' . $candidate;

            if (view()->exists($test)) {
                return $candidate;
            }
        }

        throw new \Exception("$for view for page type " . static::class . ' does not exist. Possible candidates were: ' . implode(', ', $candidates));
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
        return $absolute ? rtrim(config('app.url'), '/') . "/$this->path" : "/$this->path";
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

    public function getChildTypes(): array
    {
        return Chord::getPageTypes();
    }

    public function buildSortQuery(): Builder
    {
        return static::query()->where('parent_id', $this->parent_id);
    }
}
