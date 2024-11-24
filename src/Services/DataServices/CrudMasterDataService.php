<?php

namespace Thereline\CrudMaster\Services\DataServices;

use Thereline\CrudMaster\Contracts\DataServiceContracts\CrudMasterDataServiceContract;
use Thereline\CrudMaster\Services\DataServices;
use Thereline\CrudMaster\Traits\HasEntityHelpers;

class CrudMasterDataService implements CrudMasterDataServiceContract
{
    use DataServices\HasCreateEntity;
    use DataServices\HasDestroyEntity;
    use DataServices\HasFindAllEntities;
    use DataServices\HasFindOneEntity;
    use DataServices\HasRemoveEntity;
    use DataServices\HasUpdateEntity;
    use HasEntityHelpers;
}
