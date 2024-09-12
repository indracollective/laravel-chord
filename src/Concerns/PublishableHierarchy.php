<?php

namespace LiveSource\Chord\Concerns;

trait PublishableHierarchy
{
    use HasHierarchy;
    use Publishable;

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
}
