<?php

namespace Thereline\CrudMaster\Tests\Cleaners;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;

class ViewsCleaner extends Orchestra
{
    public static function clean(): void
    {
        $viewsBladePath = resource_path('views/TestResource');
        $viewsVuePath = resource_path('js/Pages/TestResource');

        // Delete generated views directory
        if (File::isDirectory($viewsBladePath)) {
            File::deleteDirectory($viewsBladePath);
        }

        if (File::isDirectory($viewsBladePath)) {
            File::deleteDirectory($viewsVuePath);
        }

    }
}
