<?php

namespace LiveSource\Chord\Concerns;

use Illuminate\Support\Str;
use Indra\Revisor\Facades\Revisor;
use Parental\HasParent;
use ReflectionException;

trait HasInheritance
{
    use HasParent;

    /**
     * Overrides Model::getTable to return the appropriate
     * table (draft, version, published) based on
     * the current RevisorMode
     */
    public function getTable(): string
    {
        return $this->table ?? Revisor::getSuffixedTableNameFor($this->getBaseTable());
    }

    /**
     * Get the base table name for the model
     */
    public function getBaseTable(): string
    {
        return $this->baseTable ?? Str::snake(Str::pluralStudly(class_basename($this->getParentClass())));
    }
}
