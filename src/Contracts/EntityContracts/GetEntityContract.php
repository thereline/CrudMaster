<?php

namespace Thereline\CrudMaster\Contracts\EntityContracts;

interface GetEntityContract
{
    /**
     * Find a record by ID
     */
    public function getEntity(int $id, array $withRelations = []): array|int;
}
