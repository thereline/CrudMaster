<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Services\HttpServices\CrudMasterHttpService;

beforeEach(function () {
    $this->httpService = new CrudMasterHttpService;
    $this->request = Mockery::mock(Request::class);
});

test('it returns JSON error response when result array is missing keys', function () {
    $result = ['invalid' => 'data'];
    $response = $this->httpService->responseHandler($this->request, $result, 'view');

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->status())->toBe(ResponseAlias::HTTP_INTERNAL_SERVER_ERROR)
        ->and($response->getData(true)['error'])->toBe(trans('crud-master::translations.error.500'));
});

test('it returns JSON error response for error result', function () {
    $this->request->shouldReceive('wantsJson')->andReturn(true);
    $result = ['error' => true, 'success' => false, 'status' => ResponseAlias::HTTP_BAD_REQUEST, 'data' => [], 'message' => 'Error message'];
    $response = $this->httpService->responseHandler($this->request, $result, 'view');

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->status())->toBe(ResponseAlias::HTTP_BAD_REQUEST)
        ->and($response->getData(true)['message'])->toBe('Error message');
});

test('it returns redirect error response for error result', function () {
    $this->request->shouldReceive('wantsJson')->andReturn(false);
    $this->request->shouldReceive('path')->andReturn(false);
    $result = ['error' => true, 'success' => false, 'data' => [], 'status' => ResponseAlias::HTTP_BAD_REQUEST, 'message' => 'Error message'];
    $response = $this->httpService->responseHandler($this->request, $result, 'view', 'home');

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getSession()->get('errors')->getBag('default')->first())
        ->toBe('Error message');
});

test('it returns JSON success response for success result', function () {
    $this->request->shouldReceive('wantsJson')->andReturn(true);
    $this->request->shouldReceive('path')->andReturn(true);
    $result = ['error' => false, 'success' => true, 'status' => ResponseAlias::HTTP_OK, 'data' => ['key' => 'value'], 'message' => 'Success message'];
    $response = $this->httpService->responseHandler($this->request, $result, 'view');

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->status())->toBe(ResponseAlias::HTTP_OK)
        ->and($response->getData(true)['key'])->toBe('value');
});

test('it returns Inertia response for success result', function () {

    $this->request->shouldReceive('wantsJson')->andReturn(false);
    $this->request->shouldReceive('path')->andReturn(false);
    $this->request->shouldReceive('header')->with('X-Inertia')->andReturn(true);
    $result = ['error' => false, 'success' => true, 'status' => ResponseAlias::HTTP_BAD_REQUEST, 'data' => ['key' => 'value'], 'message' => 'Success message'];

    if (! class_exists(Inertia::class)) {
        Mockery::mock('alias:Inertia');
    }

    $response = $this->httpService->responseHandler($this->request, $result, 'view');

    expect($response)->toBeInstanceOf(\Inertia\Response::class);
});

test('it returns Blade view response for success result', function () {
    $this->request->shouldReceive('wantsJson')->andReturn(false);
    $this->request->shouldReceive('path')->andReturn(false);
    $this->request->shouldReceive('header')->with('X-Inertia')->andReturn(false);
    $result = ['error' => false, 'success' => true, 'status' => ResponseAlias::HTTP_OK, 'data' => ['data' => ['key' => 'some value']], 'message' => 'Success message'];
    $response = $this->httpService->responseHandler($this->request, $result, 'test.view');

    expect($response)->toBeInstanceOf(Response::class);
    //->and($response->getContent())->toContain('some value');
});

test('it returns redirect success response', function () {
    $this->request->shouldReceive('wantsJson')->andReturn(false);
    $this->request->shouldReceive('path')->andReturn(false);
    $this->request->shouldReceive('header')->andReturn(false);
    $result = ['error' => false, 'success' => true, 'status' => ResponseAlias::HTTP_OK,  'data' => ['key' => 'value'],
        'message' => 'Success message'];
    $response = $this->httpService
        ->responseHandler($this->request, $result, 'test.view', 'home');

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->headers->get('Location'))->toBe(route('home'));
});
