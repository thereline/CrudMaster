<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;
use Workbench\App\Models\Principal;
use Workbench\App\Models\School;
use Workbench\App\Models\SchoolSession;
use Workbench\App\Models\Student;
use Workbench\App\Models\Teacher;

beforeEach(function () {
    uses(RefreshDatabase::class);
    $this->repository = new class
    {
        use \Thereline\CrudMaster\Services\DataServices\HasUpdateEntity;
    };
    $this->repository->setUpdateEntityClass(new School);
});

it('update an entity by ID', function () {
    $school = School::factory()->create(['name' => 'Old Name']);

    $result = $this->repository->updateEntity($school->id, ['name' => 'New Name']);

    expect($result)->toBeArray()
        ->and($result['name'])->toBe('New Name');
});

it('update an entity with HasOne relationship', function () {
    //Given
    $school = School::factory()->create(['name' => 'Old School']);
    $principal = $school->principal()->create(['name' => 'Old Principal']);

    $relationshipData = [
        'principal' => ['name' => 'New Principal'],
    ];

    //When
    $result = $this->repository->updateEntity($school->id, ['name' => 'Updated School'], $relationshipData, ['principal']);

    //Then
    $this->assertModelExists($school);
    $this->assertModelExists($principal);
    expect($result)->toBeArray()
        ->and($result['name'])->toBe('Updated School')
        ->and($result['principal']['name'])->toBe('New Principal');
});

it('update an entity with HasMany relationships', function () {
    //Given
    //$session = SchoolSession::factory()->create();
    $school = School::factory()->create(['name' => 'School With Teachers']);
    $teacher = Teacher::factory()->create(['school_id' => $school->id, 'name' => 'First Teacher']);
    $school->teachers()->create(['name' => 'Second Teacher'], ['name' => 'Third Teacher']);

    $relationshipData = [
        'teachers' => [['id' => $teacher->id, 'name' => 'Updated Teacher']],
    ];

    //When
    $result = $this->repository->updateEntity($school->id, [], $relationshipData, ['teachers']);

    //THen
    expect($result)->toBeArray()
        ->and($result['name'])->toBe('School With Teachers')
        ->and($result['teachers'][0]['name'])->toBe('Updated Teacher');
});

it('update an entity with BelongsTo relationship', function () {
    //Given
    $school = School::factory()->create(['name' => 'Wrong School']);
    $principal = $school->principal()->create(['name' => 'Principal Name']);
    $this->repository->setUpdateEntityClass(new Principal);

    //When
    $relationshipData = [
        'school' => [
            ['id' => $school->id, 'name' => 'Corrected School'],
        ],
    ];
    $result = $this->repository->updateEntity($principal->id, ['name' => 'Updated Principal Name'], $relationshipData, ['school']);

    //Then
    expect($result)->toBeArray()
        ->and($result['name'])->toBe('Updated Principal Name')
        ->and($result['school']['name'])->toBe('Corrected School');
});

it('update an entity with BelongsToMany relationships', function () {
    $session = SchoolSession::factory()->create();
    $school = School::factory()->create(['name' => 'School With Students']);
    $student = Student::factory()->create(['first_name' => 'Student 1']);
    $student2 = Student::factory()->create(['first_name' => 'Student 2']);
    $school->students()->syncWithPivotValues(
        [$student->id, $student2->id],
        ['session_id' => $session->id, 'enrolled_at' => now()]
    );

    $relationshipData = [
        'students' => ['id' => $student->id, 'first_name' => 'new name'],

    ];

    $result = $this->repository->updateEntity($school->id, ['name' => 'Updated School'], $relationshipData, ['students']);

    expect($result)->toBeArray()
        ->and($result['name'])->toBe('Updated School')
        ->and($result['students'][0]['first_name'])->toBe('new name')
        ->and($result['students'][0]['registrations']['school_id'])->toBe($school->id)
        ->and($result['students'][1]['first_name'])->toBe('Student 2');
});

it('update Returns not found error for invalid ID', function () {
    $result = $this->repository->updateEntity(999, ['name' => 'New Name']);

    expect($result)->toBe(ExceptionCodes::MODEL_NOT_FOUND);
});

it('returns not found error for invalid ID', function () {
    $result = $this->repository->updateEntity(999, ['name' => 'New Name']);

    expect($result)->toBe(ExceptionCodes::MODEL_NOT_FOUND);
});

it('can apply a through function after update', function () {
    //Given
    $school = School::factory()->create(['name' => 'Old Name']);
    $throughFunction = function ($entity) {
        $entity->transformed_name = strtoupper($entity->name);

        return $entity;
    };

    //When
    $result = $this->repository->updateEntity($school->id, ['name' => 'New Name'], [], [], $throughFunction);

    expect($result)->toBeArray()
        ->and($result['transformed_name'])->toBe('NEW NAME');
});

it('handles query exceptions', function () {
    //Given
    $mockModel = $this->partialMock(School::class, function (MockInterface $mock) {
        $mock->shouldReceive('newQuery')
            ->andThrow(new QueryException('', '', [], new Exception));
    });

    //When
    $this->repository->setUpdateEntityClass($mockModel);
    $result = $this->repository->updateEntity(1, []);
    //Then
    expect($result)->toBe(ExceptionCodes::DB_QUERY_ERROR);

});

it('handles any exceptions', function () {
    //Given
    $mockModel = $this->partialMock(School::class, function (MockInterface $mock) {
        $mock->shouldReceive('newQuery')
            ->andThrow(new Error);
    });

    //When
    $this->repository->setUpdateEntityClass($mockModel);
    $result = $this->repository->updateEntity(1, []);
    //Then
    expect($result)->toBe(ExceptionCodes::FATAL_ERROR);

});
