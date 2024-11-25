<?php

namespace Thereline\CrudMaster\Contracts\ActionContracts;

interface DestroyActionContract
{
    /**
     * Delete a record by ID
     */
    public function destroyAction(int|array $data): array;
}
