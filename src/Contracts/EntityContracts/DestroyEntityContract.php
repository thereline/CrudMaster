<?php

namespace Thereline\CrudMaster\Contracts\EntityContracts;

interface DestroyEntityContract
{

    /**
     * Delete a record by ID
     * @param int $id
     * @return bool|int
     */

    public function destroyEntity(int $id): bool|int;
}
