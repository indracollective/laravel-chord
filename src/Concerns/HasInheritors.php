<?php

namespace LiveSource\Chord\Concerns;

use Indra\Revisor\Facades\Revisor;
use Parental\HasChildren;

trait HasInheritors
{
    use HasChildren;


    /**
     * Overrides HasChildren::newInstance and
     * HasRevisor::newInstance to combine both
     */
    public function newInstance($attributes = [], $exists = false): self
    {
        $model = isset($attributes[$this->getInheritanceColumn()])
            ? $this->getChildModel($attributes)
            : new static(((array) $attributes));

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        $model->setRevisorMode($this->getRevisorMode() ?? Revisor::getMode());

        return $model;
    }
}
