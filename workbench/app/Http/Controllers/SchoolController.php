<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Workbench\App\Http\Requests\SchoolRequest;
use Workbench\App\Services\SchoolService;

class SchoolController extends Controller
{
    public function __construct(protected SchoolService $schoolService) {}

    public function index(Request $request): View|Response|JsonResponse|\Inertia\Response|RedirectResponse
    {
        $entity = $this->schoolService->dataService->findAllEntities();
        $model = $this->schoolService->actionService->findAllAction($entity);

        return $this->schoolService->httpService->responseHandler($request, $model);
    }

    public function store(SchoolRequest $request): View|Response|JsonResponse|\Inertia\Response|RedirectResponse
    {
        $input = $request->all();

        $entity = $this->schoolService->dataService->createEntity($input);
        $model = $this->schoolService->actionService->createAction($entity);

        return $this->schoolService->httpService->responseHandler($request, $model, 'test.view');

    }
}
