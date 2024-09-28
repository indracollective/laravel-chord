<?php

namespace LiveSource\Chord\Concerns;

use LiveSource\Chord\Models\ChordPage;

trait ManagesPagePaths
{
    protected static function bootManagesPagePaths(): void
    {
        static::creating(function (ChordPage $page) {
            $page->path = $page->generatePath();
        });

        static::updating(function (ChordPage $page) {
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

    public function generatePath(): string
    {
        if (! $this->parent_id) {
            return $this->slug;
        }

        return $this->parent->path.'/'.$this->slug;
    }
}
