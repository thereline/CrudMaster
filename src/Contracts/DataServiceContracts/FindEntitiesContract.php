<?php

namespace Thereline\CrudMaster\Contracts\DataServiceContracts;

interface FindEntitiesContract
{
    /**
     * Retrieve all records
     */
    public function findAllEntities(
        array $filters = [],
        string $orderBy = 'created_at',
        string $orderDirection = 'desc',
        int $perPage = 15,
        array $withRelations = [],
        ?callable $throughFunction = null
    ): array|int;
}
