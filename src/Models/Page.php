<?php

namespace LiveSource\Chord\Models;

use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use LiveSource\Chord\Chord;
use Livesource\Chord\PageTypes\PageType;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Page extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    protected $fillable = [
        'title',
        'slug',
        'blocks',
        'parent_id',
        'order_column',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    protected $casts = [
        'blocks' => 'array',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function buildSortQuery()
    {
        return static::query()->where('parent_id', $this->parent_id);
    }

    public function blockData(): Collection
    {
        return collect($this->blocks ?? [])->map(function ($block) {
            if (! $class = Chord::getBlockTypeClass($block['type'])) {
                throw new \Exception("Block Class for key '{$block['type']}' does not exist");
            }

            return $class::from($block['data']);
        });
    }

    public function typeObject(): ?PageType
    {
        if (! $class = Chord::getPageTypeClass($this->page_type)) {
            throw new \Exception("Page Type Class for key '{$this->page_type}' does not exist");
        }

        return $class::from($this->type_data ?? []);
    }

    public function configureForm(Form $form): void {}

    public function getLinkAttribute(): string
    {
        return $this->slug === '/' ? $this->slug : "/$this->slug";
    }
}
