<?php

namespace Thereline\CrudMaster\Contracts\ActionContracts;

use Illuminate\Database\Eloquent\Model;
use Thereline\CrudMaster\Contracts\ActionContracts;

/**
 * Interface BaseRepositoryContract
 */
interface CrudMasterActionServiceContract extends ActionContracts\CreateActionContract, ActionContracts\DestroyActionContract, ActionContracts\FindAllActionContract, ActionContracts\FindOneActionContract, ActionContracts\RemoveActionContract, ActionContracts\UpdateActionContract
{
    public function setModelName(string|null|Model $model = null): void;
}
