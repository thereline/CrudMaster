<?php

namespace Thereline\CrudMaster\Contracts\ActionContracts;

interface FindAllActionContract
{
    /**
     * Retrieve all records
     */
    public function findAllAction(array|int $data): array;
}
