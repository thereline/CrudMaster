<?php

namespace Thereline\CrudMaster\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Thereline\CrudMaster\CrudMaster
 */
class CrudMaster extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Thereline\CrudMaster\CrudMaster::class;
    }
}
