<?php

namespace Thereline\CrudMaster\Tests\Feature\Service\Entities;

use Illuminate\Database\Eloquent\Model;
use Thereline\CrudMaster\Services\EntityServices\HasCreateEntity;
use Thereline\CrudMaster\Services\EntityServices\HasGetEntities;

class TestRepository
{

    use HasCreateEntity;
    use HasGetEntities;

    function __construct(Model $model)
    {
        $this->model = $model;
        $this->setFilterBy(['name','email']);
    }

}
