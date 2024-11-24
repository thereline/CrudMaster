<?php

namespace Thereline\CrudMaster\Contracts\DataServiceContracts;

interface RemoveEntityContract
{
    /**
     * Delete a record by ID
     **/
    public function removeEntity(int $id, array $withRelations = [],
        ?callable $throughFunction = null): array|int;
}
