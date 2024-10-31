<?php

namespace Thereline\CrudMaster\Contracts\EntityContracts;

interface RemoveEntityContract
{
    /**
     * Delete a record by ID
     */
    public function removeEntity(int $id): bool|int;
}
