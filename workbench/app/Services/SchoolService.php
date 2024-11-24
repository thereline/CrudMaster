<?php

namespace Workbench\App\Services;

use Thereline\CrudMaster\Contracts\ActionContracts\CrudMasterActionServiceContract;
use Thereline\CrudMaster\Contracts\DataServiceContracts\CrudMasterDataServiceContract;
use Thereline\CrudMaster\Contracts\HttpContracts\CrudMasterHttpServiceContract;
use Workbench\App\Models\School;

class SchoolService
{
    public function __construct(
        public CrudMasterDataServiceContract $dataService,
        public CrudMasterActionServiceContract $actionService,
        public CrudMasterHttpServiceContract $httpService
    ) {
        $this->dataService->setModelClass(new School);
        $this->actionService->setModelName(School::class);

    }
}
