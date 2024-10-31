<?php

namespace Thereline\CrudMaster\Contracts;

use Thereline\CrudMaster\Contracts\EntityContracts\CreateEntityContract;
use Thereline\CrudMaster\Contracts\EntityContracts\GetEntitiesContract;

/**
 * Interface BaseRepositoryInterface
 */
interface BaseRepositoryInterface extends CreateEntityContract, GetEntitiesContract {}
