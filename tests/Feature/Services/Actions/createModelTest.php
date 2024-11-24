<?php

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Contracts\DataServiceContracts\CreateEntityContract;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;
use Thereline\CrudMaster\Services\ActionServices\HasCreateAction;

beforeEach(function () {
    $this->mockModel = Mockery::mock(Model::class);
    $this->mockService = Mockery::mock(CreateEntityContract::class);
    $this->trait = new class
    {
        use HasCreateAction;
    };
    $this->trait->setCreateActionModel($this->mockModel);
});

afterEach(function () {
    Mockery::close();
});
test('it returns success response on valid createEntity result', function () {
    $input = ['example' => 'data'];

    $response = $this->trait->createAction($input);

    expect($response)->toBe([
        'success' => true,
        'error' => false,
        'status' => ResponseAlias::HTTP_CREATED,
        'message' => trans('crud-master::translations.create.success', ['model' => get_class($this->mockModel)]),
        'data' => $input,
    ]);
});

test('it returns model relationship not found error response', function () {
    $input = ExceptionCodes::MODEL_RELATIONSHIP_NOTFOUND;

    $response = $this->trait->createAction($input);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
        'message' => trans('crud-master::translations.create.error', ['model' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});

test('it returns db unique violation error response', function () {
    $input = ExceptionCodes::DB_UNIQUE_VIOLATION_ERROR;

    $response = $this->trait->createAction($input);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
        'message' => trans('crud-master::translations.error.unique', ['model' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});

test('it returns db query error response', function () {
    $input = ExceptionCodes::DB_QUERY_ERROR;

    $response = $this->trait->createAction($input);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_BAD_REQUEST,
        'message' => trans('crud-master::translations.error.query', ['actioning' => 'creating', 'name' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});

test('it returns default error response', function () {
    $input = 9999; // Any unhandled error code

    $response = $this->trait->createAction($input);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        'message' => trans('crud-master::translations.error.500', ['actioning' => 'creating', 'name' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});
