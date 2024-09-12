<?php

namespace LiveSource\Chord\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasHierarchy
{
    public function parent(): BelongsTo;

    public function children(): HasMany;

    public function hasChildren(): bool;

    public function getAllowedChildTypes(): array;
}
