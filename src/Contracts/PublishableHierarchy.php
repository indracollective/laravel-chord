<?php

namespace LiveSource\Chord\Contracts;

interface PublishableHierarchy
{
    public function publishRecursively(): static;

    public function unpublishRecursively(): static;
}
