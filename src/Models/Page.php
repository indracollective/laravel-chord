<?php

namespace LiveSource\Chord\Models;

use Exception;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LiveSource\Chord\Facades\Chord as ChordFacade;
use LiveSource\Chord\PageTypes\PageType;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Page extends Model implements Sortable
{
    use SortableTrait;

    protected $fillable = [
        'title',
        'slug',
        'page_data',
        'parent_id',
        'order_column',
        'type',
    ];

    protected $casts = [
        'page_data' => 'array',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    /**
     * @throws Exception
     */
    public function getData(): PageType
    {
        // todo - this should be cached
        $type = $this->getRawOriginal('type');
        if (! $class = ChordFacade::getPageTypeClass($type)) {
            throw new Exception("Page Type Class for key '$type' does not exist. Registered types are " . implode(', ', array_keys(ChordFacade::getPageTypes())));
        }

        return $class::from($this->getRawOriginal('page_data') ?? []);
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function buildSortQuery(): Builder
    {
        return static::query()->where('parent_id', $this->parent_id);
    }

    public function configureForm(Form $form): void {}

    public function getLinkAttribute(): string
    {
        return $this->slug === '/' ? $this->slug : "/$this->slug";
    }
}
