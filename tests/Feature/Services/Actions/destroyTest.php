<?php

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Contracts\DataServiceContracts\DestroyEntityContract;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;
use Thereline\CrudMaster\Services\ActionServices\HasDestroyAction;

beforeEach(function () {
    $this->mockModel = Mockery::mock(Model::class);
    $this->mockService = Mockery::mock(DestroyEntityContract::class);
    $this->trait = new class
    {
        use HasDestroyAction;
    };
    $this->trait->setDestroyActionModel($this->mockModel);
});

afterEach(function () {
    Mockery::close();
});

test('it returns success response on valid destroyEntity result', function () {
    $data = ['id' => 1];

    $response = $this->trait->destroyAction($data);

    expect($response)->toBe([
        'success' => true,
        'error' => false,
        'status' => ResponseAlias::HTTP_OK,
        'message' => trans('crud-master::translations.delete.success', ['model' => get_class($this->mockModel)]),
        'data' => [],
    ]);
});

test('it returns model not found error response', function () {

    $data = ExceptionCodes::MODEL_NOT_FOUND;

    $response = $this->trait->destroyAction($data);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_NOT_FOUND,
        'message' => trans('crud-master::translations.delete.error', ['model' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});

test('it returns db query error response', function () {

    $data = ExceptionCodes::DB_QUERY_ERROR;

    $response = $this->trait->destroyAction($data);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_BAD_REQUEST,
        'message' => trans('crud-master::translations.error.query', ['actioning' => 'deleting', 'name' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});

test('it returns default error response for unknown error codes', function () {

    $data = ExceptionCodes::FATAL_ERROR;

    $response = $this->trait->destroyAction($data);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        'message' => trans('crud-master::translations.error.500', ['actioning' => 'deleting', 'name' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});
