<?php

namespace Thereline\CrudMaster\Contracts\EntityContracts;

interface CreateEntityContract
{

    /**
     * Create a new record
     * @param array $data
     * @param array $relationships
     * @return array|int
     */
    public function createEntity(array $data, array $relationships = []) : array|int;

}
