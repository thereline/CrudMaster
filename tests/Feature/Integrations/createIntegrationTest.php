<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Thereline\CrudMaster\Contracts\ActionContracts\CrudMasterActionServiceContract;
use Thereline\CrudMaster\Contracts\DataServiceContracts\CrudMasterDataServiceContract;
use Thereline\CrudMaster\Contracts\HttpContracts\CrudMasterHttpServiceContract;
use Workbench\App\Models\School;

beforeEach(function () {

    uses(RefreshDatabase::class);
    //$this->model =  new School();
    $this->httpService = app(CrudMasterHttpServiceContract::class); //new CrudMasterHttpService();
    $this->dataService = app(CrudMasterDataServiceContract::class); //new CrudMasterDataService();
    $this->actionService = app(CrudMasterActionServiceContract::class);

    $this->dataService->setModelClass(new School);
    $this->actionService->setModelName(School::class);

});

it('creates records with no relationships successfully for API', function () {
    //Given
    $request = Request::create('/api/schools', 'POST', [
        'name' => 'Test School',
        'email' => 'test@example.com',
    ]);

    //When
    $entity = $this->dataService->createEntity($request->all());
    $model = $this->actionService->createAction($entity);
    $response = $this->httpService->responseHandler($request, $model);

    //Then
    expect($response->status())->toBe(Response::HTTP_CREATED)
        ->and($response->content())->toBeJson()
        ->and($response->content())->toContain('id');

    // Assuming it redirects to 'users.index'
    $this->assertDatabaseHas('schools', [
        'name' => 'Test School',
        'email' => 'test@example.com',
    ]);

});

it('creates record with array relationships with pivot successfully for API', function () {
    //Given
    $request = Request::create('/api/schools', 'POST', [
        'name' => 'Test School',
        'email' => 'test@example.com',
    ]);

    $relationships = [
        'students' => [
            ['first_name' => 'Student One', 'active' => true, 'pivot' => ['session_id' => 1, 'enrolled_at' => now()]],
            ['first_name' => 'Student Two', 'active' => false, 'pivot' => ['session_id' => 1, 'enrolled_at' => now()]],
        ],
    ];
    //When
    $entity = $this->dataService->createEntity($request->all(), $relationships);
    $model = $this->actionService->createAction($entity);
    $response = $this->httpService->responseHandler($request, $model);

    //Then
    expect($response->status())->toBe(Response::HTTP_CREATED)
        ->and($response->content())->toBeJson();

    // Assuming it redirects to 'users.index'
    $this->assertDatabaseHas('schools', [
        'name' => 'Test School',
        'email' => 'test@example.com',
    ]);

    $this->assertDatabaseHas('students', [
        'first_name' => 'Student One',
        'active' => true,
    ]);

});

it('creates record with no relationships successfully for Web', function () {
    //Given
    $request = Request::create('/schools', 'POST', [
        'name' => 'Test School',
        'email' => 'test@example.com',
    ]);

    //When
    $entity = $this->dataService->createEntity($request->all());
    $model = $this->actionService->createAction($entity);
    $response = $this->httpService->responseHandler($request, $model, 'test.view');

    //Then
    expect($response->status())->toBe(Response::HTTP_CREATED)
        ->and($response->content())->toContain('Test School');

    // Assuming it redirects to 'users.index'
    $this->assertDatabaseHas('schools', [
        'name' => 'Test School',
        'email' => 'test@example.com',
    ]);
});

it('creates record with array relationships with pivot successfully for Web', function () {
    //Given
    $request = Request::create('/schools', 'POST', [
        'name' => 'Test School',
        'email' => 'test@example.com',
    ]);

    $relationships = [
        'students' => [
            ['first_name' => 'Student One', 'active' => true, 'pivot' => ['session_id' => 1, 'enrolled_at' => now()]],
            ['first_name' => 'Student Two', 'active' => false, 'pivot' => ['session_id' => 1, 'enrolled_at' => now()]],
        ],
    ];
    //When
    $entity = $this->dataService->createEntity($request->all(), $relationships);
    $model = $this->actionService->createAction($entity);

    $response = $this->httpService->responseHandler($request, $model, 'test.view');

    //Then
    expect($response->status())->toBe(Response::HTTP_CREATED)
        ->and($response->content())->toContain('Test School');

    $this->assertDatabaseHas('schools', [
        'name' => 'Test School',
        'email' => 'test@example.com',
    ]);

    $this->assertDatabaseHas('students', [
        'first_name' => 'Student One',
        'active' => true,
    ]);

});

it('handles  create error response for API', function () {

    //Given
    $request = Request::create('/api/schools', 'POST', [
        'email' => 'johndoe@example.com',
    ]);

    //When
    $entity = $this->dataService->createEntity($request->all());
    $model = $this->actionService->createAction($entity);
    $response = $this->httpService->responseHandler($request, $model);

    //Then
    expect($response->status())->toBe(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->and($response->content())->toBeJson()
        ->and($response->content())->not()->toContain('error=>true');

    // Assuming it redirects to 'users.index'
    $this->assertDatabaseEmpty('schools');
});

it('handles create  error response for Web', function () {

    //Given
    $request = Request::create('/users', 'POST', [
        'email' => 'johndoe@example.com',
        'password' => 'password123',
    ]);

    //When
    $entity = $this->dataService->createEntity($request->all());
    $model = $this->actionService->createAction($entity);
    $response = $this->httpService->responseHandler($request, $model);

    //Then
    expect($response->status())->toBe(302)
        ->and($response->content())->toContain('Redirecting');

    // Assuming it redirects to 'users.index'
    $this->assertDatabaseEmpty('schools');
});
