<?php

namespace Thereline\CrudMaster\Contracts\EntityContracts;

interface RemoveEntityContract
{

    /**
     * Delete a record by ID
     * @param int $id
     * @return bool|int
     */

    public function removeEntity(int $id): bool|int;
}
