<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;
use Thereline\CrudMaster\Contracts\ActionContracts\CrudMasterActionServiceContract;
use Thereline\CrudMaster\Contracts\DataServiceContracts\CrudMasterDataServiceContract;
use Thereline\CrudMaster\Contracts\HttpContracts\CrudMasterHttpServiceContract;
use Workbench\App\Models\School;
use Workbench\App\Models\SchoolSession;
use Workbench\App\Models\Student;

beforeEach(function () {

    uses(RefreshDatabase::class);
    $this->httpService = app(CrudMasterHttpServiceContract::class); //new CrudMasterHttpService();
    $this->dataService = app(CrudMasterDataServiceContract::class); //new CrudMasterDataService();
    $this->actionService = app(CrudMasterActionServiceContract::class);

    $this->dataService->setModelClass(new School);
    $this->actionService->setModelName(School::class);

});

it('finds one record with no relationships successfully for API', function () {
    //Given

    $school = School::factory()->create(['name' => 'School 1']);
    $request = Request::create('/api/schools', 'GET', [
        'id' => $school->id,
    ]);

    //When
    $entity = $this->dataService->findOneEntity($request->integer('id'));
    $model = $this->actionService->findOneAction($entity);

    $response = $this->httpService->responseHandler($request, $model);

    //Then
    expect($response->status())->toBe(Response::HTTP_FOUND)
        ->and($response->content())->toBeJson()
        ->and($response->content())->toContain('School 1');

    // Assuming it redirects to 'users.index'
    $this->assertDatabaseHas('schools', [
        'name' => 'School 1',
    ]);

});

it('finds one record with array relationships with pivot successfully for API', function () {
    //Given
    $session = SchoolSession::factory()->create();
    $school = School::factory()->create(['name' => 'School With Students']);
    $student = Student::factory()->create(['first_name' => 'Student 1']);
    $school->students()->syncWithPivotValues(
        [$student->id],
        ['session_id' => $session->id, 'enrolled_at' => now()]
    );
    $request = Request::create('/api/schools', 'POST', [
        'id' => $school->id,
    ]);

    //When
    $entity = $this->dataService->findOneEntity($request->integer('id'), ['students']);
    $model = $this->actionService->findOneAction($entity);
    $response = $this->httpService->responseHandler($request, $model);
    $original = $response->original;

    //Then
    expect($response->status())->toBe(Response::HTTP_FOUND)
        ->and($original['students'][0]['first_name'])->toBe('Student 1');

});

it('finds one record with no relationships successfully for Web', function () {
    //Given
    $session = SchoolSession::factory()->create();
    $school = School::factory()->create(['name' => 'School With Students']);
    $student = Student::factory()->create(['first_name' => 'Student 1']);
    $school->students()->syncWithPivotValues(
        [$student->id],
        ['session_id' => $session->id, 'enrolled_at' => now()]
    );
    $request = Request::create('/schools', 'GET', [
        'id' => $school->id,
    ]);

    //When
    $entity = $this->dataService->findOneEntity($request->integer('id'), ['students']);
    $model = $this->actionService->findOneAction($entity);
    $response = $this->httpService->responseHandler($request, $model, 'test.view');

    //Then
    expect($response->status())->toBe(Response::HTTP_FOUND)
        ->and($response->content())->toContain('School With Students');

});

it('handles  find one error response for API', function () {

    //Given
    $request = Request::create('/api/schools', 'GET', [
        'id' => 9999,
    ]);

    //When
    $entity = $this->dataService->findOneEntity($request->integer('id'));
    $model = $this->actionService->findOneAction($entity);
    $response = $this->httpService->responseHandler($request, $model);

    //Then
    expect($response->status())->toBe(Response::HTTP_NOT_FOUND)
        ->and($response->content())->toBeJson()
        ->and($response->content())->toContain('"error":true');

});

it('handles find one  error response for Web', function () {

    //Given
    $request = Request::create('/schools', 'GET', [
        'id' => 9999,
    ]);

    //When
    $entity = $this->dataService->findOneEntity($request->integer('id'));
    $model = $this->actionService->findOneAction($entity);
    $response = $this->httpService->responseHandler($request, $model);

    //Then
    expect($response->status())->toBe(302)
        ->and($response->content())->toContain('Redirecting');

});
