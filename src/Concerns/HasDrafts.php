<?php

namespace LiveSource\Chord\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Oddvalue\LaravelDrafts\Concerns\HasDrafts as OddvalueHasDrafts;

trait HasDrafts
{
    use OddvalueHasDrafts;

    /**
     * Overridden to add the `withoutGlobalScope` method to the relation.
     * Allows displaying all revisions in preview mode
     **/
    public function revisions(): HasMany
    {
        return $this->hasMany(static::class, $this->getUuidColumn(), $this->getUuidColumn())
            ->withDrafts()
            ->withoutGlobalScope('onlyCurrentInPreviewMode');
    }

    /**
     * Extra method to get the publish status attribute.
     */
    public function getStatusAttribute(): string
    {
        if ($this->hasPublishedVersion()) {
            return $this->isPublished() ? 'published' : 'revised';
        }

        return 'draft';
    }

    /**
     * Extra method to check if the page has a published version.
     */
    public function hasPublishedVersion(): bool
    {
        if ($this->isPublished()) {
            return true;
        }

        return static::withDrafts()
            ->where('uuid', $this->uuid)
            ->whereNot('id', $this->id)
            ->published()
            ->count() > 0;
    }

    /**
     * Overridden to unpublish all other revisions before publishing this one.
     * And a few other extras...
     **/
    public function publish(): static
    {
        // don't publish if the model is already published
        if ($this->isPublished()) {
            return $this;
        }

        if ($this->fireModelEvent('publishing') === false) {
            return $this;
        }

        // Unpublish all other revisions and publish this one
        $this::withoutTimestamps(fn () => $this->revisions()
            ->where('is_published', true)
            ->update(['is_published' => false]));

        // Update the published at column
        $this->{$this->getPublishedAtColumn()} ??= now();

        // don't create a new revision when publishing
        $this->withoutRevision();

        // ensure the publisher is set
        $this->setPublisher();

        // now the standard OddValue behaviour
        $this->setPublishedAttributes();

        static::saved(function (Model $model): void {
            if ($model->isNot($this)) {
                return;
            }

            $this->fireModelEvent('published');
        });

        $this->save();

        return $this;
    }

    public function publishRecursively(): static
    {
        $this->publish();

        $this->children()->get()->each(fn ($child) => $child->publishRecursively());

        return $this;
    }

    public static function parentsWithUnpublishedChildren(): Collection
    {
        return static::class::current()->onlyDrafts()->whereNotNull('parent_id')->pluck('parent_id');
    }
}
