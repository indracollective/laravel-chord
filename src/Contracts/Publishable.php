<?php

namespace LiveSource\Chord\Contracts;

interface Publishable
{
    public function publish(): static;

    public function unpublish(): static;

    public function isPublished(): bool;
}
