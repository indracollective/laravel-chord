<?php

namespace LiveSource\Chord\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use LiveSource\Chord\Models\ChordPage;

class ChordPagePolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return true;
    }

    public function previewAny(Authenticatable $user): bool
    {
        return auth()->check();
    }

    public function view(Authenticatable $user, ChordPage $chordPage): bool
    {
        return true;
    }

    public function create(Authenticatable $user): bool
    {
        return auth()->check();
    }

    public function update(Authenticatable $user, ChordPage $chordPage): bool
    {
        return auth()->check();
    }

    public function delete(Authenticatable $user, ChordPage $chordPage): bool
    {
        return auth()->check();
    }

    public function restore(Authenticatable $user, ChordPage $chordPage): bool
    {
        return auth()->check();
    }

    public function forceDelete(Authenticatable $user, ChordPage $chordPage): bool
    {
        return auth()->check();
    }
}
