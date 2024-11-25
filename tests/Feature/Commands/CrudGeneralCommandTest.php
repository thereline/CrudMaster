<?php

use Illuminate\Support\Facades\File;
use Thereline\CrudMaster\Tests\Cleaners;

afterEach(function () {
    Cleaners\ModelCleaner::clean();
    Cleaners\ControllerCleaner::clean();
    Cleaners\ServiceCleaner::clean();
    Cleaners\RouteCleaner::clean();
    Cleaners\ViewsCleaner::clean();
});

it('generates CRUD files for a given resource', function () {

    // Mocking user input for resource name and columns
    $this->artisan('crudmaster:generate', ['--name' => 'TestResource'])
        //->expectsQuestion('What is the name of the resource?', 'TestResource')
        ->expectsQuestion('What configuration should be used?', 'input')
        ->expectsQuestion('Which design structure should be used?', 'standard')
        ->expectsConfirmation('With migration?', 'yes')
        ->expectsConfirmation('With factory?', 'yes')
        ->expectsConfirmation('With seeder?', 'yes')
        ->expectsQuestion('Which routes should be generated?', 'both')
        ->expectsQuestion('Where should the route be placed?', 'merge')
        ->expectsQuestion('Which view should be generated?', 'vue')
        ->expectsQuestion('How many columns do you want to add to the model?', 0)
        ->expectsOutput('Generating CRUD for: TestResource')
        ->assertSuccessful();

    // Assert that the expected files are created
    $modelPath = app_path('Models/TestResource.php');
    $controllerPath = app_path('Http/Controllers/TestResourceController.php');
    $servicePath = app_path('Services/TestResourceService.php');
    $webRoutesPath = base_path('routes/web.php');
    $apiRoutesPath = base_path('routes/api.php');
    //dd( $webRoutesPath);

    expect(File::exists($modelPath))->toBeTrue()
        ->and(File::exists($controllerPath))->toBeTrue()
        ->and(File::exists($servicePath))->toBeTrue()
        ->and(File::exists($webRoutesPath))->toBeTrue()
        //->toContain('Route::resource(\'testresources\', TestResourceController::class);')
        ->and(File::exists($apiRoutesPath))->toBeTrue();
    //->toContain('Route::resource(\'testresources\', TestResourceController::class);');
});

it('generates CRUD files from config file', function () {

    // When

    $this->artisan('crudmaster:generate', ['--name' => 'TestResource'])
        ->expectsQuestion('What configuration should be used?', 'file')
        ->expectsQuestion('How many columns do you want to add to the model?', 0)
        ->assertSuccessful();

    // Then
    $modelPath = app_path('Models/TestResource.php');
    $controllerPath = app_path('Http/Controllers/TestResourceController.php');
    $servicePath = app_path('Services/TestResourceService.php');
    $webRoutesPath = base_path('routes/web.php');
    $apiRoutesPath = base_path('routes/api.php');

    expect(File::exists($modelPath))->toBeTrue()
        ->and(File::exists($controllerPath))->toBeTrue()
        ->and(File::exists($servicePath))->toBeTrue()
        ->and(File::exists($webRoutesPath))->toBeTrue()
        //->toContain('Route::resource(\'testresources\', TestResourceController::class);')
        ->and(File::exists($apiRoutesPath))->toBeTrue();
    //->toContain('Route::resource(\'testresources\', TestResourceController::class);');
});
