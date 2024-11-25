<?php

namespace Thereline\CrudMaster\Services\ActionServices;

use Thereline\CrudMaster\Contracts\ActionContracts\CrudMasterActionServiceContract;
use Thereline\CrudMaster\Services\ActionServices;
use Thereline\CrudMaster\Traits\HasActionHelpers;

class CrudMasterActionService implements CrudMasterActionServiceContract
{
    use ActionServices\HasCreateAction;
    use ActionServices\HasDestroyAction;
    use ActionServices\HasFindAllAction;
    use ActionServices\HasFindOneAction;
    use ActionServices\HasRemoveAction;
    use ActionServices\HasUpdateAction;
    use HasActionHelpers;
}
