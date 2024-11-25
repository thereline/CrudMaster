<?php

namespace Workbench\App\Services\UserServices;

use Thereline\CrudMaster\Contracts\DataServiceContracts\CrudMasterDataServiceContract;
use Thereline\CrudMaster\Services\ActionServices\CrudMasterActionService;
use Workbench\App\Models\User;

class UserService extends CrudMasterActionService
{
    public function __construct(User $user, CrudMasterDataServiceContract $dataServiceContract)
    {
        parent::__construct($user, $dataServiceContract);

    }
}
