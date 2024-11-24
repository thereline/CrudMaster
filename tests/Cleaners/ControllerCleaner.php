<?php

namespace Thereline\CrudMaster\Tests\Cleaners;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;

class ControllerCleaner extends Orchestra
{
    public static function clean(): void
    {
        $controllerPath = app_path('Http/Controllers/TestResourceController.php');
        // Delete generated controller
        if (File::exists($controllerPath)) {
            File::delete($controllerPath);
        }
    }
}
