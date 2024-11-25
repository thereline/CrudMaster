<?php

function cleanModel(): void
{
    $modelPath = app_path('Models/TestResource.php');
    $migrationPath = database_path('migrations');
    $seederPath = database_path('seeders/TestResourceSeeder.php');
    $factoryPath = database_path('factories/TestResourceFactory.php');

    // Delete generated migrations
    if (File::exists($migrationPath)) {
        File::delete($migrationPath);
    }
    // Delete generated seeder
    if (File::exists($seederPath)) {
        File::delete($seederPath);
    }
    // Delete generated factory
    if (File::exists($factoryPath)) {
        File::delete($factoryPath);
    }

    // Delete generated model
    if (File::exists($modelPath)) {
        File::delete($modelPath);
    }

    // Delete all migration files that were created during the test
    if (File::isDirectory($migrationPath)) {
        $files = File::files($migrationPath);
        foreach ($files as $file) {
            if (preg_match('/_create_testresource_table\.php$/', $file)) {
                File::delete($file);
            }
        }
    }

}

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Thereline\CrudMaster\Tests\Cleaners\ModelCleaner;

afterEach(function () {

    ModelCleaner::clean();

});

it('generates a model for the given resource with options', function () {
    Artisan::call('crudmaster:generate-model', [
        'resource' => 'TestResource',
        '--migration' => true,
        '--factory' => true,
        '--seeder' => true,
        '--columns' => 'name:string,email:string',
    ]);

    $modelPath = app_path('Models/TestResource.php');
    $migrationPath = app_path('Models/TestResource.php');
    $seederPath = database_path('seeders/TestResourceSeeder.php');
    $factoryPath = database_path('factories/TestResourceFactory.php');
    expect(File::exists($modelPath))->toBeTrue()
        ->and(File::exists($factoryPath))->toBeTrue()
        ->and(File::exists($seederPath))->toBeTrue();
});

it('generates a model without column options', function () {
    Artisan::call('crudmaster:generate-model', [
        'resource' => 'TestResource',
        '--migration' => true,
        '--factory' => true,
        '--seeder' => true,
        '--columns' => false,
    ]);

    $modelPath = app_path('Models/TestResource.php');
    $migrationPath = app_path('Models/TestResource.php');
    $seederPath = database_path('seeders/TestResourceSeeder.php');
    $factoryPath = database_path('factories/TestResourceFactory.php');
    expect(File::exists($modelPath))->toBeTrue()
        ->and(File::exists($factoryPath))->toBeTrue()
        ->and(File::exists($seederPath))->toBeTrue();
});
