<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;
use Thereline\CrudMaster\Services\DataServices\HasRemoveEntity;
use Workbench\App\Models\School;

beforeEach(function () {
    uses(RefreshDatabase::class);
    $this->repository = new class
    {
        use HasRemoveEntity;
    };
    $this->repository->setRemoveEntityClass(new School);
});

it('remove an entity by ID', function () {
    School::factory(5)->create();
    $school = School::factory()->create(['name' => 'Old Name']);

    $result = $this->repository->removeEntity($school->id);

    expect($result)->toBeArray()
        ->and($result['name'])->toBe('Old Name');

    $this->assertSoftDeleted($school);
});

it('update Returns not found error for invalid ID', function () {
    $result = $this->repository->removeEntity(999);

    expect($result)->toBe(ExceptionCodes::MODEL_NOT_FOUND);
});

it('returns not found error for invalid ID', function () {
    $result = $this->repository->removeEntity(999);

    expect($result)->toBe(ExceptionCodes::MODEL_NOT_FOUND);
});

it('handles query exceptions', function () {
    //Given
    $mockModel = $this->partialMock(School::class, function (MockInterface $mock) {
        $mock->shouldReceive('newQuery')
            ->andThrow(new QueryException('', '', [], new Exception));
    });

    //When
    $this->repository->setRemoveEntityClass($mockModel);
    $result = $this->repository->removeEntity(1);
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
    $this->repository->setRemoveEntityClass($mockModel);
    $result = $this->repository->removeEntity(1);
    //Then
    expect($result)->toBe(ExceptionCodes::FATAL_ERROR);

});
