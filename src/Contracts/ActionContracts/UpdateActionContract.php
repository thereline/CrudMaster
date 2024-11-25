<?php

namespace Thereline\CrudMaster\Contracts\ActionContracts;

interface UpdateActionContract
{
    /**
     * Update a record by ID
     */
    public function updateAction(array|int $data): array;
}
