<?php

namespace Thereline\CrudMaster\Contracts\ActionContracts;

interface CreateActionContract
{
    public function createAction(array|int $data): array;
}
