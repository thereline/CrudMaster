<?php

namespace Workbench\App\Repositories;

use Thereline\CrudMaster\Contracts\DataServiceContracts\CreateEntityContract;
use Thereline\CrudMaster\Contracts\DataServiceContracts\FindEntitiesContract;
use Thereline\CrudMaster\Contracts\DataServiceContracts\FindEntityContract;
use Thereline\CrudMaster\Services\DataServices\HasCreateEntity;
use Thereline\CrudMaster\Services\DataServices\HasFindAllEntities;
use Thereline\CrudMaster\Services\DataServices\HasFindOneEntity;
use Thereline\CrudMaster\Services\DataServices\HasUpdateEntity;
use Workbench\App\Models\Principal;

class PrincipalRepository implements CreateEntityContract, FindEntitiesContract, FindEntityContract
{
    public function __construct(Principal $model)
    {
        $this->setModel($model);
        $this->setFilterBy(['name']);
        $this->setRelationsAndFilterBy(['school' => ['name']]);
    }

    use HasCreateEntity;
    use HasFindAllEntities;
    use HasFindOneEntity;
    use HasUpdateEntity;
}
