<?php

namespace LiveSource\Chord\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasHierarchy
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    public function publishRecursively(): static
    {
        $this->publish();

        $this->children()->get()->each(fn ($child) => $child->publishRecursively());

        return $this;
    }

    public function unpublishRecursively(): static
    {
        $this->unpublish();

        $this->children()->get()->each(fn ($child) => $child->unpublishRecursively());

        return $this;
    }

    public function getAllowedChildTypes(): array
    {
        return [];
    }
}
