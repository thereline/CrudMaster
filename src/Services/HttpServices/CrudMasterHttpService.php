<?php

namespace Thereline\CrudMaster\Services\HttpServices;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Contracts\HttpContracts\CrudMasterHttpServiceContract;

class CrudMasterHttpService implements CrudMasterHttpServiceContract
{
    /**
     * Handle different types of responses based on DataService results.
     */
    public function responseHandler(
        Request $request,
        array $result,
        ?string $view = null,
        ?string $redirectRoute = null,
        array $headers = []
    ): Response|JsonResponse|View|RedirectResponse|\Inertia\Response {
        if (! isset($result['error'], $result['success'], $result['data'], $result['message'])) {
            return response()->json(
                ['error' => trans('crud-master::translations.error.500')],
                ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($result['error']) {
            return $this->handleError($request, $result, $headers);
        }

        if ($result['success']) {

            return $this->handleSuccess($request, $result, $view, $redirectRoute, $headers);
        }

        // Handle unexpected result types
        return response()->json(
            ['error' => trans('crud-master::translations.error.result-format')],
            ResponseAlias::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    private function handleError(
        Request $request,
        array $result,
        array $headers = []

    ): JsonResponse|RedirectResponse {
        $errorData = [
            'error' => true,
            'message' => $result['message'],
        ];

        // Handle API request
        if ($request->wantsJson() || Str::contains($request->path(), 'api')) {
            return response()->json($errorData, $result['status'], $headers);
        }

        // Redirect to a given back for web requests
        return redirect()->back()->withErrors($result['message']);

    }

    private function handleSuccess(
        Request $request,
        array $result,
        ?string $view = null,
        ?string $redirectRoute = null,
        array $headers = []
    ): Response|JsonResponse|\Inertia\Response|RedirectResponse {
        // Handle API request
        if ($request->wantsJson() || Str::contains($request->path(), 'api')) {

            return response()->json($result['data'], $result['status'], $headers);
        }

        // Handle Inertia.js request
        if ($request->header('X-Inertia')) {
            if (class_exists('\Inertia\Inertia')) {
                return Inertia::render($view, $result['data']);
            }

            return response()->json(
                ['error' => trans('crud-master::translations.error.initial-missing')],
                $result['status']
            );
        }

        if ($redirectRoute) {
            return redirect()->route($redirectRoute)->with('success', $result['message']);
        }

        // Default to a Blade view response
        return response()->view($view, ['data' => $result['data']], $result['status'], $headers);

    }
}
