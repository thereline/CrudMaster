<?php

namespace Thereline\CrudMaster\Tests\Feature\Service\Entities;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Thereline\CrudMaster\ExceptionCodes;
use Thereline\CrudMaster\Services\EntityServices\HasCreateEntity;
use Workbench\App\Models\School;


uses(RefreshDatabase::class);


it('creates a entity with no relationships successfully', function () {
    // Given a School and related Students
    $data = ['name' => 'Test School', 'email' => 'test@example.com'];
    $repository = new TestRepository(new \Workbench\App\Models\School());


    // When
    $result = $repository->createEntity($data);

    // Then
    expect($result)
        ->toBeArray()
        ->and($result['name'])->toEqual('Test School')
        ->and($result['email'])->toEqual('test@example.com');


});

it('creates a entity with array relationships successfully', function () {
    // Given a School and related Students
    $data = ['name' => 'Test School', 'email' => 'test@example.com'];
    $relationships = [
        'students' => [
            ['first_name' => 'Student One', 'active' => true],
            ['first_name' => 'Student Two', 'active' => false],
        ],
    ];
    $repository = new TestRepository(new \Workbench\App\Models\School());

    // When
    $result = $repository->createEntity($data, $relationships);

    // Then
    expect($result)
        ->toBeArray()
        ->and($result['name'])->toEqual('Test School')
        // Ensure departments are created
        ->and($result['students'])->toHaveLength(2)
        ->and($result['students'][0]['first_name'])->toEqual('Student One')
        ->and($result['students'][1]['first_name'])->toEqual('Student Two');

});

it('creates a entity with single relationships successfully', function () {
    // Given a School and related Students
    $data = ['name' => 'Test School', 'email' => 'test@example.com'];
    $relationships = [
        'students' => ['first_name' => 'Student One']
    ];
    $repository = new TestRepository(new \Workbench\App\Models\School());

    // When
    $result = $repository->createEntity($data, $relationships);

    // Then
    expect($result)
        ->toBeArray()
        ->and($result['name'])->toEqual('Test School')
        // Ensure departments are created
        ->and($result['students'])->toHaveLength(1)
        ->and($result['students'][0]['first_name'])->toEqual('Student One');

});

it('handles unique constraint violations for relationships', function () {

    // Given existing data that violates a unique constraint
    School::factory()->create(['email' => 'duplicate@example.com']);
    $data = ['name' => 'Another School', 'email' => 'duplicate@example.com'];
    $relationships = [
        'students' => ['first_name' => 'John Doe']
    ];

    // Mock UniqueConstraintViolationException for primary model save
    $mockModel = $this->mock(School::class, function (MockInterface $mock) use ($data) {
        $mock->shouldReceive('fill')->with($data)->andReturnSelf();
        $mock->shouldReceive('saveOrFail')
            ->andThrow(new UniqueConstraintViolationException('conn', 'sql', [], new Exception));
    });
    $repository = new TestRepository($mockModel);

    // When
    $result = $repository->createEntity($data, $relationships);

    // Then
    expect($result)->toEqual(ExceptionCodes::DB_UNIQUE_VIOLATION_ERROR);
});

it('handles database query exceptions during relationship creation', function () {
    // Given data for a School
    $data = ['name' => 'Test School', 'email' => 'test@example.com'];
    $relationships = [
        'students' => ['name' => 'Student One']

    ];

    // Mock QueryException for relationship save
    $mockModel = $this->partialMock(School::class, function (MockInterface $mock) {
        $mock->shouldReceive('saveOrFail')
            ->andThrow(new QueryException('conn', 'sql', [], new Exception));
    });
    $repository = new TestRepository($mockModel);

    // When
    $result = $repository->createEntity($data, $relationships);

    // Then
    expect($result)->toEqual(ExceptionCodes::DB_QUERY_ERROR);
});

it('handles relationship not found exceptions during relationship creation', function () {
    // Given data for a School
    $data = ['name' => 'Test School', 'email' => 'test@example.com'];
    $relationships = [
        'undefined' => ['name' => 'Student One']

    ];

    $repository = new TestRepository(new School());

    // When
    $result = $repository->createEntity($data, $relationships);

    // Then
    expect($result)->toEqual(ExceptionCodes::MODEL_RELATIONSHIP_NOTFOUND);
});

it('handles other exceptions during creation', function () {
    // Given data for a School
    $data = ['name' => 'Test School', 'email' => 'test@example.com'];

    // Mock QueryException for relationship save
    $mockModel = $this->partialMock(School::class, function (MockInterface $mock) {
        $mock->shouldReceive('saveOrFail')
            ->andThrow(new Exception());
    });
    $repository = new TestRepository($mockModel);

    // When
    $result = $repository->createEntity($data);

    // Then
    expect($result)->toEqual(ExceptionCodes::FATAL_ERROR);
});
