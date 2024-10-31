<?php

namespace Thereline\CrudMaster\Contracts\EntityContracts;

interface GetEntitiesContract
{

    /**
     * Retrieve all records
     * @param array $filters
     * @param string $orderBy
     * @param string $orderDirection
     * @param int $perPage
     * @param array $withRelations
     * @param callable|null $throughFunction
     * @return array|int
     */
    public function getEntities(
        array $filters = [],
        string $orderBy = 'created_at',
        string $orderDirection = 'desc',
        int $perPage = 15,
        array $withRelations = [],
        ?callable $throughFunction = null
    ) : array|int;

}
