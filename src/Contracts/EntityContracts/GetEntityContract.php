<?php

namespace Thereline\CrudMaster\Contracts\EntityContracts;

interface GetEntityContract
{

    /**
     * Find a record by ID
     * @param int $id
     * @param array $withRelations
     * @return array|int
     */
    public function getEntity(int $id, array $withRelations = []): array | int;


}
