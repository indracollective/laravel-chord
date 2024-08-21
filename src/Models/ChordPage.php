<?php

namespace LiveSource\Chord\Models;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LiveSource\Chord\Facades\Chord;
use LiveSource\Chord\Filament\Actions\EditPageSettingsAction;
use LiveSource\Chord\Filament\Resources\PageResource;
use Parental\HasChildren as HasInheritors;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ChordPage extends Model implements Sortable
{
    use HasInheritors;
    use SortableTrait;

    protected $table = 'chord_pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta',
        'parent_id',
        'order_column',
        'type',
    ];

    protected $casts = [
        'content' => 'array',
        'meta' => 'array',
    ];

    public function contentForm(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title'),
        ]);
    }

    public function getTableRecordURL(Table $table): ?string
    {
        return PageResource::getUrl('edit', ['record' => $this->id]);
    }

    public function afterCreateRedirectURL(): string
    {
        return PageResource::getUrl('edit', ['record' => $this->id]);
    }

    public function settingsAction(): ?EditPageSettingsAction
    {
        return EditPageSettingsAction::make();
    }

    public function hasContentForm(): bool
    {
        return true;
    }

    public function getLinkAttribute(): string
    {
        return $this->slug === '/' ? $this->slug : "/$this->slug";
    }

    public function getChildTypes(): array
    {
        return Chord::getPageTypes();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChordPage::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChordPage::class, 'parent_id');
    }

    public function buildSortQuery(): Builder
    {
        return static::query()->where('parent_id', $this->parent_id);
    }

    public static function getLabel(): string
    {
        return str((new \ReflectionClass(static::class))->getShortName())->headline()->toString();
    }

    public static function getDefaultKey(): string
    {
        return str((new \ReflectionClass(static::class))->getShortName())->toString();
    }
}
