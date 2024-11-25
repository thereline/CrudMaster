<?php

namespace Thereline\CrudMaster\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasActionHelpers
{
    protected string|null|Model $modelName = null;

    public function setModelName(string|null|Model $model = null): void
    {

        $class = is_object($model) ? get_class($model) : $model;

        $this->modelName = basename(str_replace('\\', '/', $class));

    }

    public function getModelName(): Model|string|null
    {
        return $this->modelName;
    }
}
