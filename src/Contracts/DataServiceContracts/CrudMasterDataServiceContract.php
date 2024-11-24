<?php

namespace Thereline\CrudMaster\Contracts\DataServiceContracts;

use Illuminate\Database\Eloquent\Model;
use Thereline\CrudMaster\Contracts\DataServiceContracts;

/**
 * Interface BaseRepositoryContract
 */
interface CrudMasterDataServiceContract extends DataServiceContracts\CreateEntityContract, DataServiceContracts\DestroyEntityContract, DataServiceContracts\FindEntitiesContract, DataServiceContracts\FindEntityContract, DataServiceContracts\RemoveEntityContract, DataServiceContracts\UpdateEntityContract
{
    public function setModelClass(?Model $model = null): void;
}
