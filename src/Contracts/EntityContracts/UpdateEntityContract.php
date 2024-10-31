<?php

namespace Thereline\CrudMaster\Contracts\EntityContracts;

interface UpdateEntityContract
{

    /**
     * Update a record by ID
     * @param array $data
     * @param array $relationships
     * @return array|int
     */
    public function updateEntity(array $data, array $relationships = []): array|int;

}
