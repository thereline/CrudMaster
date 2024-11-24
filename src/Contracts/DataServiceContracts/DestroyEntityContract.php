<?php

namespace Thereline\CrudMaster\Contracts\DataServiceContracts;

interface DestroyEntityContract
{
    /**
     * Delete a record by ID
     */
    public function destroyEntity(int $id, array $withRelations = [],
        ?callable $throughFunction = null): array|int;
}
