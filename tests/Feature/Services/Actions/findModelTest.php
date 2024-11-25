<?php

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Thereline\CrudMaster\Contracts\DataServiceContracts\FindEntityContract;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;
use Thereline\CrudMaster\Services\ActionServices\HasFindOneAction;

beforeEach(function () {
    $this->mockModel = Mockery::mock(Model::class);
    $this->mockService = Mockery::mock(FindEntityContract::class);
    $this->trait = new class
    {
        use HasFindOneAction;
    };
    $this->trait->setFindActionModel($this->mockModel);

});

afterEach(function () {
    Mockery::close();
});

test('it returns success response on valid getEntity result', function () {
    $data = ['id' => 1, 'name' => 'John Doe'];

    $response = $this->trait->findOneAction($data);

    expect($response)->toBe([
        'success' => true,
        'error' => false,
        'status' => ResponseAlias::HTTP_FOUND,
        'message' => trans('crud-master::translations.getOne.success', ['model' => get_class($this->mockModel)]),
        'data' => $data,
    ]);
});

test('it returns model not found error response', function () {
    $data = ExceptionCodes::MODEL_NOT_FOUND;

    $response = $this->trait->findOneAction($data);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_NOT_FOUND,
        'message' => trans('crud-master::translations.getOne.error', ['model' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});

test('it returns db query error response', function () {
    $data = ExceptionCodes::DB_QUERY_ERROR;

    $response = $this->trait->findOneAction($data);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_BAD_REQUEST,
        'message' => trans('crud-master::translations.error.query', ['actioning' => 'getting', 'name' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});

test('it returns default error response', function () {
    $data = 99999;

    $response = $this->trait->findOneAction($data);

    expect($response)->toBe([
        'data' => [],
        'status' => ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        'message' => trans('crud-master::translations.error.500', ['actioning' => 'getting', 'name' => get_class($this->mockModel)]),
        'error' => true,
        'success' => false,
    ]);
});
