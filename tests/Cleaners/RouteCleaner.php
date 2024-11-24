<?php

namespace Thereline\CrudMaster\Tests\Cleaners;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;

class RouteCleaner extends Orchestra
{
    public static function clean(): void
    {
        $webRoutesPath = base_path('routes/web.php');
        $apiRoutesPath = base_path('routes/api.php');

        $separateWebRoutesPath = base_path('routes/web');
        $separateApiRoutesPath = base_path('routes/api');

        // Remove generated routes from web.php
        if (File::exists($webRoutesPath)) {
            $webRoutesContent = File::get($webRoutesPath);
            $webRoutesContent = preg_replace("/Route::resource\('testresources', TestResourceController::class\);\n?/", '', $webRoutesContent);
            File::put($webRoutesPath, $webRoutesContent);
        }

        // Remove generated routes from api.php
        if (File::exists($apiRoutesPath)) {
            $apiRoutesContent = File::get($apiRoutesPath);
            $apiRoutesContent = preg_replace("/Route::resource\('testresources', TestResourceController::class\);\n?/", '', $apiRoutesContent);
            File::put($apiRoutesPath, $apiRoutesContent);
        }

        if (File::isDirectory($separateWebRoutesPath)) {
            File::deleteDirectory($separateWebRoutesPath);
        }
        if (File::isDirectory($separateApiRoutesPath)) {
            File::deleteDirectory($separateApiRoutesPath);
        }
    }
}
