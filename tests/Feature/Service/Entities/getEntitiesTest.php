<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Thereline\CrudMaster\Tests\Feature\Service\Entities\TestRepository;
use Workbench\App\Models\School;

uses(RefreshDatabase::class);

it('retrieves models successfully', function () {
    // Given
    $schools = School::factory()->create(); // Create a schools using a factory
    $repo = new TestRepository(new School);

    // When
    $results = $repo->getEntities();

    // Then
    expect($results)->toBeArray()
        // Check if we retrieved one school
        ->and($results['data'])->toHaveLength(1)
        ->and($results['data'][0]['id'])->toEqual($schools->id)
        ->and($results['data'][0]['name'])->toEqual($schools->name);

});

it('applies filters correctly', function () {
    // Given
    School::factory()->create(['name' => 'First School', 'email' => 'first@example.com']);
    School::factory()->create(['name' => 'Second School', 'email' => 'second@example.com']);
    $repo = new TestRepository(new School);

    // When
    $results = $repo->getEntities(['search' => 'First School']);

    // Then
    expect($results)->toBeArray()
        // Check if we retrieved one school
        ->and($results['data'])->toHaveLength(1)
        // Check the name
        ->and($results['data'][0]['name'])->toEqual('First School');

});
