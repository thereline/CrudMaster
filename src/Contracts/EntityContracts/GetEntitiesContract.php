<?php

namespace Thereline\CrudMaster\Contracts\EntityContracts;

interface GetEntitiesContract
{
    /**
     * Retrieve all records
     */
    public function getEntities(
        array $filters = [],
        string $orderBy = 'created_at',
        string $orderDirection = 'desc',
        int $perPage = 15,
        array $withRelations = [],
        ?callable $throughFunction = null
    ): array|int;
}
