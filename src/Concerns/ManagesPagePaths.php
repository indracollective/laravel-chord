<?php

namespace LiveSource\Chord\Concerns;

use Illuminate\Database\Eloquent\Model;
use LiveSource\Chord\Models\ChordPage;

trait ManagesPagePaths
{
    protected static function bootManagesPagePaths(): void
    {
        static::saving(function (ChordPage $page) {
            // generate the slug if it doesn't exist yet
            if (! $page->slug) {
                $page->slug = str($page->title)->slug();
            }
            // update the path if it doesn't exist or may have changed
            if (! $page->isDirty('slug') && ! $page->isDirty('parent_id')) {
                return;
            }
            $page->path = $page->generatePath();
        });

        // update the path of all children if the path of the parent changes
        static::saved(function (ChordPage $page) {
            if ($page->isDirty('path') && $page->children()->count() > 0) {
                $page->children()->each(function (ChordPage $child) {
                    $child->update(['path' => $child->generatePath()]);
                });
            }
        });
    }

    public static function generateSlug(?Model $record = null, ?string $fromString = null, ?int $parentId = null): string
    {
        $fromString = $fromString ?? $record?->title;
        $parentId = $parentId ?? $record?->parent_id;
        $slug = $fromString === 'Home' ? '/' : str($fromString)->slug();

        return static::ensureSlugIsUnique($slug, $parentId);
    }

    public static function ensureSlugIsUnique(string $slug, ?int $parentId = null): string
    {
        $counter = 1;
        $originalSlug = $slug;

        while (static::query()->where('parent_id', $parentId)->where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function generatePath(): string
    {
        if (! $this->parent_id) {
            return $this->slug;
        }

        return $this->parent->path.'/'.$this->slug;
    }
}
