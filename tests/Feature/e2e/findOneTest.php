<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Workbench\App\Models\School;

beforeEach(function () {
    uses(RefreshDatabase::class);
    $this->school = School::factory(10)->create();
});

it('find one records with no relationships successfully for API', function () {
    //Given
    $response = $this->get('api/v1/schools', [
        'id' => 1,
    ]);

    $response->dd();

    //Then
    expect($response->status())->toBe(Response::HTTP_FOUND)
        ->and($response->content())->toBeJson()
        ->and($response->content())->toContain($this->school);

})->todo();
