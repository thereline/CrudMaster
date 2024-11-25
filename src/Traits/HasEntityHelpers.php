<?php

namespace Thereline\CrudMaster\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasEntityHelpers
{
    protected ?Model $modelClass = null;

    public function setModelClass(?Model $model = null): void
    {
        $this->modelClass = $model;
    }

    public function getModelClass(): ?Model
    {
        return $this->modelClass;
    }
}
