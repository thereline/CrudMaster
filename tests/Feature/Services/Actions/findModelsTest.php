<?php

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;
use Thereline\CrudMaster\Services\ActionServices\HasFindAllAction;

beforeEach(function () {
    $this->mockModel = Mockery::mock(Model::class);
    $this->trait = new class
    {
        use HasFindAllAction;
    };
    $this->trait->setFindAllActionModel($this->mockModel);
});

afterEach(function () {
    Mockery::close();
});

test('it returns success response on valid getEntities result', function () {
    $entityData = ['id' => 1, 'name' => 'first name'];

    $response = $this->trait->findAllAction($entityData);

    expect($response)->toBe([
        'success' => true,
        'error' => false,
        'status' => ResponseAlias::HTTP_OK,
        'message' => trans('crud-master::translations.getAll.success', ['model' => get_class($this->mockModel)]),
        'data' => $entityData,
    ]);
});

test('it returns fatal error response', function () {

    $entityData = ExceptionCodes::FATAL_ERROR;

    $response = $this->trait->findAllAction($entityData);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        'message' => trans('crud-master::translations.getAll.error', ['model' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});

test('it returns db query error response', function () {
    $entityData = ExceptionCodes::DB_QUERY_ERROR;

    $response = $this->trait->findAllAction($entityData);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_BAD_REQUEST,
        'message' => trans('crud-master::translations.error.query', ['actioning' => 'getting', 'name' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});

test('it returns default error response', function () {

    $entityData = 9999;

    $response = $this->trait->findAllAction($entityData);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        'message' => trans('crud-master::translations.error.500', ['actioning' => 'getting', 'name' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});
