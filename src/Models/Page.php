<?php

namespace LiveSource\Chord\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'blocks',
        'parent_id'
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

    public function blockData(): Collection
    {
        return collect($this->blocks ?? [] )->map(function ($block) {
            $type = $block['type'] ?? null;
            $obj = $type::from($block['data']);
            return $obj;
        });
    }

    public function getLinkAttribute(): string
    {
        return $this->slug === '/' ? $this->slug : "/$this->slug";
    }
}
