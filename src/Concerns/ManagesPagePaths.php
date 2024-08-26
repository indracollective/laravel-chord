<?php

namespace LiveSource\Chord\Concerns;

use LiveSource\Chord\Models\ChordPage;

trait ManagesPagePaths
{
    protected static function bootManagesPagePaths(): void
    {
        // update the path if it doesn't exist or may have changed
        static::saving(function (ChordPage $page) {
            if (! $page->isDirty('slug') && ! $page->isDirty('parent_id')) {
                return;
            }
            $page->path = static::generatePath($page);
        });

        // update the path of all children if the path of the parent changes
        static::saved(function (ChordPage $page) {
            if ($page->isDirty('path') && $page->children()->count() > 0) {
                $page->children()->each(function (ChordPage $child) {
                    $child->update(['path' => static::generatePath($child)]);
                });
            }
        });
    }

    public static function generatePath(ChordPage $page): string
    {
        if (! $page->parent_id) {
            return $page->slug;
        }

        return $page->parent->path . '/' . $page->slug;
    }
}
