<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Thereline\CrudMaster\Tests\Cleaners\ControllerCleaner;

afterEach(function () {

    ControllerCleaner::clean();

});

it('generates a controller for the given resource', function () {
    Artisan::call('crudmaster:generate-controller', [
        'resource' => 'TestResource',
        'serviceKind' => 'simple',
    ]);

    $controllerPath = app_path('Http/Controllers/TestResourceController.php');
    expect(File::exists($controllerPath))->toBeTrue();
});
