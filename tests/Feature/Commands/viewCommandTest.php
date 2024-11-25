<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Thereline\CrudMaster\Tests\Cleaners\ViewsCleaner;

afterEach(function () {
    // ViewsCleaner::clean();
});

it('generates blade views for the given resource', function () {
    Artisan::call('crudmaster:generate-views', [
        'name' => 'TestResource',
        '--blade' => true,
        '--tailwind' => true,
    ]);

    $indexPath = resource_path('views/TestResource/index.blade.php');
    $createPath = resource_path('views/TestResource/create.blade.php');
    $editPath = resource_path('views/TestResource/edit.blade.php');
    $showPath = resource_path('views/TestResource/show.blade.php');

    expect(File::exists($indexPath))->toBeTrue()
        ->and(File::exists($createPath))->toBeTrue()
        ->and(File::exists($editPath))->toBeTrue()
        ->and(File::exists($showPath))->toBeTrue();
});

it('generates vue views for the given resource', function () {
    Artisan::call('crudmaster:generate-views', [
        'name' => 'TestResource',
        '--vue' => true,
        '--tailwind' => true,
    ]);

    $indexPath = resource_path('js/Pages/TestResource/Index.vue');
    $createPath = resource_path('js/Pages/TestResource/Create.vue');
    $editPath = resource_path('js/Pages/TestResource/Edit.vue');
    $showPath = resource_path('js/Pages/TestResource/Show.vue');

    expect(File::exists($indexPath))->toBeTrue()
        ->and(File::exists($createPath))->toBeTrue()
        ->and(File::exists($editPath))->toBeTrue()
        ->and(File::exists($showPath))->toBeTrue();
});
