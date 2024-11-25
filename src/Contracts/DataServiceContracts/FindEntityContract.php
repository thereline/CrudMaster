<?php

namespace Thereline\CrudMaster\Contracts\DataServiceContracts;

interface FindEntityContract
{
    /**
     * Find a record by ID
     */
    public function findOneEntity(
        int $id,
        array $withRelations = [],
        ?callable $throughFunction = null

    ): array|int;
}
