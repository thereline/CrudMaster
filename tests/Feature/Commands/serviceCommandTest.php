<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Thereline\CrudMaster\Tests\Cleaners\ServiceCleaner;

afterEach(function () {

    //ServiceCleaner::clean();

});

it('generates a service for the given resource', function () {
    Artisan::call('crudmaster:generate-service', [
        'resource' => 'TestResource',
    ]);

    $servicePath = app_path('Services/TestResourceService.php');
    expect(File::exists($servicePath))->toBeTrue();
});
