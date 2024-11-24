<?php

namespace Thereline\CrudMaster\Contracts\HttpContracts;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface CrudMasterHttpServiceContract
{
    public function responseHandler(
        Request $request,
        array $result,
        string $view,
        ?string $redirectRoute = null,
        array $headers = []
    ): Response|JsonResponse|View|RedirectResponse|\Inertia\Response;
}
