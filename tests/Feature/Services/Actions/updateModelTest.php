<?php

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;
use Thereline\CrudMaster\Services\ActionServices\HasUpdateAction;

beforeEach(function () {
    $this->mockModel = Mockery::mock(Model::class);
    $this->trait = new class
    {
        use HasUpdateAction;
    };
    $this->trait->setUpdateActionModel($this->mockModel);
});

afterEach(function () {
    Mockery::close();
});

test('it returns success response on valid updateEntity result', function () {
    $input = ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'];

    $response = $this->trait->updateAction($input);

    expect($response)->toBe([
        'success' => true,
        'error' => false,
        'status' => ResponseAlias::HTTP_OK,
        'message' => trans('crud-master::translations.update.success', ['model' => get_class($this->mockModel)]),
        'data' => $input,
    ]);
});

test('it returns model not found error response', function () {
    $input = ExceptionCodes::MODEL_NOT_FOUND;

    $response = $this->trait->updateAction($input);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_NOT_FOUND,
        'message' => trans('crud-master::translations.update.error', ['model' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});

test('it returns db unique violation error response', function () {
    $input = ExceptionCodes::DB_UNIQUE_VIOLATION_ERROR;

    $response = $this->trait->updateAction($input);

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

    $response = $this->trait->updateAction($input);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_BAD_REQUEST,
        'message' => trans('crud-master::translations.error.query', ['actioning' => 'updating', 'name' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});

test('it returns default error response', function () {
    $input = 9999;

    $response = $this->trait->updateAction($input);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        'message' => trans('crud-master::translations.error.500', ['actioning' => 'updating', 'name' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});
