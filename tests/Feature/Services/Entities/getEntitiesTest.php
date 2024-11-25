<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Thereline\CrudMaster\Exceptions\ExceptionCodes;
use Thereline\CrudMaster\Services\DataServices\HasFindAllEntities;
use Workbench\App\Models\School;

use function Pest\Laravel\partialMock;

beforeEach(function () {
    uses(RefreshDatabase::class);
    $this->repository = new class
    {
        use HasFindAllEntities;
    };
    $this->repository->setFindEntitiesClass(new School);
});

test('it can retrieve entities with default parameters', function () {
    //Given
    School::factory()->count(20)->create();

    //when
    $result = $this->repository->findAllEntities();

    //THen
    expect($result)->toBeArray()
        // Default perPage is 15
        ->and(count($result['data']))->toBe(15);

});

it('retrieves models successfully', function () {
    // Given
    $schools = School::factory()->create(); // Create a schools using a factory

    // When
    $results = $this->repository->findAllEntities();

    // Then
    expect($results)->toBeArray()
        // Check if we retrieved one school
        ->and($results['data'])->toHaveLength(1)
        ->and($results['data'][0]['id'])->toEqual($schools->id)
        ->and($results['data'][0]['name'])->toEqual($schools->name);

});

test('it can apply search filters', function () {
    //Given
    School::factory()->count(10)->create();
    School::factory()->create(['name' => 'Specific Name', 'email' => 'specific@example.com']);

    //When
    $this->repository->setFilterBy(['name', 'email']);
    $this->repository->setRelationsAndFilterBy([
        'students' => ['first_name'],
        'teachers' => ['name'],
    ]);
    $result = $this->repository->findAllEntities(['search' => 'Specific Name']);
    //Then
    expect($result)->toBeArray()
        ->and(count($result['data']))->toBe(1)
        ->and($result['data'][0]['name'])->toBe('Specific Name');
});

test('it can apply active filters', function () {
    //Given
    School::factory(5)->create(['active' => true]);
    School::factory(5)->create(['active' => false]);

    //When
    $result = $this->repository->findAllEntities(['active' => false]);

    //Then
    expect($result)->toBeArray()
        ->and(count($result['data']))->toBe(5);
});

it('retrieves models with relation successfully', function () {
    // Given
    $school = School::factory()->create(['name' => 'First School', 'email' => 'first@example.com']);
    $school = School::factory()->create(['name' => 'Second School', 'email' => 'first@example.com']);
    $school->teachers()->create(['name' => 'John']);

    // When
    $results = $this->repository->findAllEntities(withRelations: ['teachers']);

    // Then
    expect($results)->toBeArray()
        // Check if we retrieved one school
        ->and($results['data'])->toHaveLength(2)
        // Check the name
        ->and($results['data'][0]['name'])->toEqual('First School')
        ->and($results['data'][1]['teachers'][0]['name'])->toEqual('John');

});

it('applies filters correctly with relation', function () {
    // Given
    $school = School::factory()->create(['name' => 'First School', 'email' => 'first@example.com']);
    $school = School::factory()->create(['name' => 'Second School', 'email' => 'first@example.com']);
    $school->teachers()->create(['name' => 'John']);

    // When
    $this->repository->setFilterBy(['name', 'email']);
    $this->repository->setRelationsAndFilterBy([
        'teachers' => ['name'],
    ]);
    $results = $this->repository->findAllEntities(['search' => 'John'], withRelations: ['teachers']);

    // Then
    expect($results)->toBeArray()
        // Check if we retrieved one school
        ->and($results['data'])->toHaveLength(1)
        // Check the name
        ->and($results['data'][0]['name'])->toEqual('Second School');

});

test('it can handle trashed filters', function () {
    //Given
    School::factory()->count(5)->create();
    School::factory()->count(5)->create(['deleted_at' => now()]);

    //When
    $resultWithTrashed = $this->repository->findAllEntities(['trashed' => 'with']);
    $resultOnlyTrashed = $this->repository->findAllEntities(['trashed' => 'only']);

    expect(count($resultWithTrashed['data']))->toBe(10)
        ->and(count($resultOnlyTrashed['data']))->toBe(5);
});

test('it can apply ordering', function () {
    School::factory()->create(['created_at' => now()->subDays(1)]);
    School::factory()->create(['created_at' => now()]);

    $result = $this->repository->findAllEntities([], 'created_at', 'asc');

    expect($result['data'][0]['created_at'])
        ->toBeLessThan($result['data'][1]['created_at']);
});

test('it can apply relationship filters', function () {
    //Given
    $school = School::factory()->create(['name' => 'School With Students']);
    $school->teachers()->create(['name' => 'Specific Name']);

    //When
    $result = $this->repository->findAllEntities(['search' => 'Specific Name'], 'created_at', 'desc', 15, ['teachers']);

    //Then
    expect($result)->toBeArray()
        ->and($result['data'][0]['teachers'][0]['name'])->toBe('Specific Name');
});

test('it can apply a through function', function () {

    //Given
    School::factory()->create(['name' => 'School 1']);
    School::factory()->create(['name' => 'School 2']);

    //When
    $throughFunction = function (\Illuminate\Support\Collection $collection) {
        return $collection->map(function ($item) {
            $item->transformed_name = strtoupper($item->name);

            return $item;
        });
    };
    $result = $this->repository->findAllEntities([], 'created_at', 'desc', 15, [], $throughFunction);

    //Then
    expect($result)->toBeArray()
        ->and(count($result['data']))->toBe(2)
        ->and($result['data'][0]['transformed_name'])->toBe('SCHOOL 1')
        ->and($result['data'][1]['transformed_name'])->toBe('SCHOOL 2');
});

test('it handles query exceptions', function () {
    //$this->expectException(QueryException::class);
    $modelMock = partialMock(School::class, function (MockInterface $mock) {
        $mock->shouldReceive('newQuery')
            ->andThrow(new QueryException('', '', [], new Exception));
    });

    $this->repository->setFindEntitiesClass($modelMock);
    $resullt = $this->repository->findAllEntities(['invalid_column' => 'value']);

    expect($resullt)->toBe(ExceptionCodes::DB_QUERY_ERROR);
});

test('it handles other exceptions', function () {
    //$this->expectException(QueryException::class);
    $modelMock = partialMock(School::class, function (MockInterface $mock) {
        $mock->shouldReceive('newQuery')
            ->andThrow(new Error);
    });
    //

    $this->repository->setFindEntitiesClass($modelMock);
    $resullt = $this->repository->findAllEntities(['invalid_column' => 'value']);

    expect($resullt)->toBe(ExceptionCodes::FATAL_ERROR);
});
