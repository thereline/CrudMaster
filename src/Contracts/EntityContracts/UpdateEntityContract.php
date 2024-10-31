<?php

namespace Thereline\CrudMaster\Contracts\EntityContracts;

interface UpdateEntityContract
{
    /**
     * Update a record by ID
     */
    public function updateEntity(array $data, array $relationships = []): array|int;
}
