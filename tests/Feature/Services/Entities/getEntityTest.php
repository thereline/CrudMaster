<?php

use Illuminate\Database\QueryException;
use Mockery\MockInterface;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;
use Thereline\CrudMaster\Services\DataServices\HasFindOneEntity;
use Workbench\App\Models\School;
use Workbench\App\Models\SchoolSession;
use Workbench\App\Models\Student;

use function Pest\Laravel\partialMock;

beforeEach(function () {

    $this->repository = new class
    {
        use HasFindOneEntity;
    };
    $this->repository->setFindEntityClass(new School);
});

test('it can retrieve a single entity by ID', function () {
    $school = School::factory()->create(['name' => 'School 1']);

    $result = $this->repository->findOneEntity($school->id);

    expect($result)->toBeArray()
        ->and($result['name'])->toBe('School 1');
});

test('it can retrieve a single entity with relationships', function () {

    $session = SchoolSession::factory()->create();
    $school = School::factory()->create(['name' => 'School With Students']);
    $student = Student::factory()->create(['first_name' => 'Student 1']);
    $school->students()->syncWithPivotValues(
        [$student->id],
        ['session_id' => $session->id, 'enrolled_at' => now()]
    );

    $result = $this->repository->findOneEntity($school->id, ['students']);

    expect($result)->toBeArray()
        ->and($result['students'][0]['first_name'])->toBe('Student 1');
});

test('it returns not found error for invalid ID', function () {
    $result = $this->repository->findOneEntity(999);

    expect($result)->toBe(ExceptionCodes::MODEL_NOT_FOUND);
});

test('it can apply a through function', function () {
    $school = School::factory()->create(['name' => 'Easy School', 'email' => '1@email.com']);

    // Define a through function to transform the entity
    $throughFunction = function ($entity) {
        $entity->transformed_name = strtoupper($entity->name);

        return $entity;
    };

    $result = $this->repository->findOneEntity($school->id, [], $throughFunction);

    expect($result)->toBeArray()
        ->and($result['transformed_name'])->toBe('EASY SCHOOL');
});

test('it handles query exceptions', function () {
    //Given
    $mockModel = partialMock(School::class, function (MockInterface $mock) {
        $mock->shouldReceive('findOrFail')
            ->andThrow(new QueryException('', '', [], new Exception));
    });

    //When
    $this->repository->setFindEntityClass($mockModel);
    $result = $this->repository->findOneEntity(1);
    //Then
    expect($result)->toBe(ExceptionCodes::DB_QUERY_ERROR);

});

test('it handles any exceptions', function () {
    //Given
    $mockModel = partialMock(School::class, function (MockInterface $mock) {
        $mock->shouldReceive('newQuery')
            ->andThrow(new Error);
    });

    //When
    $this->repository->setFindEntityClass($mockModel);
    $result = $this->repository->findOneEntity(1);
    //Then
    expect($result)->toBe(ExceptionCodes::FATAL_ERROR);

});
