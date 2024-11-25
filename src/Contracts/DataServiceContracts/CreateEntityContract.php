<?php

namespace Thereline\CrudMaster\Contracts\DataServiceContracts;

interface CreateEntityContract
{
    /**
     * Create a new record
     */
    public function createEntity(array $data, array $relationships = []): array|int;
}
