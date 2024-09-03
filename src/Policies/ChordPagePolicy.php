<?php

namespace LiveSource\Chord\Policies;

use App\Models\User;
use LiveSource\Chord\Models\ChordPage;

class ChordPagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function previewAny(User $user): bool
    {
        return auth()->check();
    }

    public function view(User $user, ChordPage $chordPage): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return auth()->check();
    }

    public function update(User $user, ChordPage $chordPage): bool
    {
        return auth()->check();
    }

    public function delete(User $user, ChordPage $chordPage): bool
    {
        return auth()->check();
    }

    public function restore(User $user, ChordPage $chordPage): bool
    {
        return auth()->check();
    }

    public function forceDelete(User $user, ChordPage $chordPage): bool
    {
        return auth()->check();
    }
}
