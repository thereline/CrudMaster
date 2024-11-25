<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

beforeEach(function () {
    uses(RefreshDatabase::class);
});

it('creates records with no relationships successfully for API', function () {

    $response = $this->post('api/v1/schools', [
        'name' => 'Test School',
        'email' => 'test@example.com',
    ]);
    //$response->dd();

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
    $relationships = [
        'students' => [
            ['first_name' => 'Student One', 'active' => true, 'pivot' => ['session_id' => 1, 'enrolled_at' => now()]],
            ['first_name' => 'Student Two', 'active' => false, 'pivot' => ['session_id' => 1, 'enrolled_at' => now()]],
        ],
    ];

    //When
    $response = $this->post('api/v1/schools',
        [
            'name' => 'Test School',
            'email' => 'test@example.com',
            $relationships,
        ],

    );

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

})->Todo();

it('creates record with no relationships successfully for Web', function () {
    //Given

    $response = $this->post('schools', [
        'name' => 'Test School',
        'email' => 'test@example.com',
    ]);

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

    $relationships = [
        'students' => [
            ['first_name' => 'Student One', 'active' => true, 'pivot' => ['session_id' => 1, 'enrolled_at' => now()]],
            ['first_name' => 'Student Two', 'active' => false, 'pivot' => ['session_id' => 1, 'enrolled_at' => now()]],
        ],
    ];

    $response = $this->post('schools', [
        'name' => 'Test School',
        'email' => 'test@example.com',
        $relationships,
    ]);

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

})->Todo();

it('handles  create error response for API', function () {

    $response = $this->post('api/v1/schools', [
        'email' => 'test@example.com',
    ]);
    //Then
    expect($response->status())->toBe(302)
        ->and($response->content())->not()->toContain('error');

    // Assuming it redirects to 'users.index'
    $this->assertDatabaseEmpty('schools');
});

it('handles create  error response for Web', function () {

    $response = $this->post('schools', [
        'email' => 'test@example.com',
    ]);

    //Then
    expect($response->status())->toBe(302)
        ->and($response->content())->toContain('Redirecting');

    // Assuming it redirects to 'users.index'
    $this->assertDatabaseEmpty('schools');
});
