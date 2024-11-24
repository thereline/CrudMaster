<?php

namespace Thereline\CrudMaster\Contracts\ActionContracts;

interface FindOneActionContract
{
    /**
     * Retrieve a single model instance by its ID with optional relationships and callback function.
     */
    public function findOneAction(int|array $data): array;
}
