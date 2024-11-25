<?php

namespace Thereline\CrudMaster\Tests\Cleaners;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;

class ModelCleaner extends Orchestra
{
    public static function clean(): void
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
}
