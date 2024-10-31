<?php

namespace Thereline\CrudMaster;

use Illuminate\Database\Eloquent\Model;

trait HasCrudMastery
{

    private Model $model;
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

}
