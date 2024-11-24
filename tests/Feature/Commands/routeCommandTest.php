<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Thereline\CrudMaster\Tests\Cleaners\RouteCleaner;

afterEach(function () {

    RouteCleaner::clean();

});

it('generates merge routes for the given resource', function () {
    Artisan::call('crudmaster:generate-routes', [
        'resource' => 'TestResource',
        '--merge' => true,
    ]);

    $webRoutesPath = base_path('routes/web.php');
    $apiRoutesPath = base_path('routes/api.php');
    expect(File::get($webRoutesPath))
        ->toContain('TestResourceController::class')
        ->and(File::get($apiRoutesPath))
        ->toContain('TestResourceController::class');
});

it('generates separate routes for the given resource', function () {
    Artisan::call('crudmaster:generate-routes', [
        'resource' => 'TestResource',
        '--merge' => false,
    ]);

    $webRoutesPath = base_path('routes/web/testresource.php');
    $apiRoutesPath = base_path('routes/api/testresource.php');
    expect(File::get($webRoutesPath))
        ->toContain('TestResourceController::class')
        ->and(File::get($apiRoutesPath))
        ->toContain('TestResourceController::class');
});
