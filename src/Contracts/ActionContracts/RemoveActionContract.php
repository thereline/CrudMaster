<?php

namespace Thereline\CrudMaster\Contracts\ActionContracts;

interface RemoveActionContract
{
    /**
     * Delete a record by ID
     */
    public function removeAction(int|array $data): array;
}
