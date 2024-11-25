<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Thereline\CrudMaster\Contracts\HttpContracts\CrudMasterHttpServiceContract;
use Thereline\CrudMaster\Services\HttpServices\CrudMasterHttpService;
use Workbench\App\Contracts\UserServiceContracts\UserActionServiceContract;
use Workbench\App\Contracts\UserServiceContracts\UserDataServiceContract;
use Workbench\App\Http\Requests\SchoolRequest;
use Workbench\App\Models\User;
use Workbench\App\Services\UserServices\UserActionService;
use Workbench\App\Services\UserServices\UserDataService;

class UserController extends Controller
{
    protected CrudMasterHttpServiceContract $httpService;

    protected UserDataServiceContract $dataService;

    protected UserActionServiceContract $userService;

    public function __construct(
        /*protected HttpServiceContract       $httpService,
        protected UserDataServiceContract   $dataService,
        protected UserActionServiceContract $userService,*/
    ) {
        $userModel = new User;

        $this->httpService = new CrudMasterHttpService;
        $this->dataService = new UserDataService($userModel);
        $this->userService = new UserActionService($userModel, $this->dataService);

    }

    /**
     * @throws ValidationException
     */
    public function store(SchoolRequest $request): \Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Inertia\Response|\Illuminate\Http\RedirectResponse
    {
        $input = $request->all();

        $entity = $this->dataService->createEntity($input);
        $model = $this->userService->createAction($entity);

        return $this->httpService->responseHandler($request, $model, '');

    }
}
