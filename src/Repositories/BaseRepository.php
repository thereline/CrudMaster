<?php

namespace Thereline\CrudMaster\Repositories;

use Illuminate\Database\Eloquent\Model;
use Thereline\CrudMaster\Contracts\BaseRepositoryInterface;
use Thereline\CrudMaster\Services\EntityServices\HasCreateEntity;
use Thereline\CrudMaster\Services\EntityServices\HasGetEntities;

class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(Model $model)
    {
        $this->model = $model;

    }

    use HasCreateEntity;
    use HasGetEntities;
}
