<?php

namespace Thereline\CrudMaster\Tests\Cleaners;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;

class ServiceCleaner extends Orchestra
{
    public static function clean(): void
    {
        $servicePath = app_path('Services/TestResourceService.php');

        // Delete generated service
        if (File::exists($servicePath)) {
            File::delete($servicePath);
        }

    }
}
