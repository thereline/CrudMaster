<?php

namespace Thereline\CrudMaster\Contracts;

use Thereline\CrudMaster\Contracts\EntityContracts\CreateEntityContract;
use Thereline\CrudMaster\Contracts\EntityContracts\GetEntitiesContract;

/**
 * Interface BaseRepositoryInterface
 * @package Thereline\CrudMaster\Repositories
 */
interface BaseRepositoryInterface
    extends CreateEntityContract, GetEntitiesContract
{

}
