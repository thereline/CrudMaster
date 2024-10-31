<?php

namespace Thereline\CrudMaster\Contracts\EntityContracts;

interface DestroyEntityContract
{
    /**
     * Delete a record by ID
     */
    public function destroyEntity(int $id): bool|int;
}
