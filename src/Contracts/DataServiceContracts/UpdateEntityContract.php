<?php

namespace Thereline\CrudMaster\Contracts\DataServiceContracts;

interface UpdateEntityContract
{
    /**
     * Update a record by ID
     */
    public function updateEntity(int $id,
        array $data,
        array $relationshipData = [],
        array $withRelations = [],
        ?callable $throughFunction = null): array|int;
}
