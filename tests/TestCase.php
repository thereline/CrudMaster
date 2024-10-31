<?php

namespace Thereline\CrudMaster\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Thereline\CrudMaster\CrudMasterServiceProvider;
use function Orchestra\Testbench\workbench_path;


class TestCase extends Orchestra
{
    use InteractsWithViews;
    use WithWorkbench;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Thereline\\CrudMaster\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Workbench\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            CrudMasterServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'CM' => \Thereline\CrudMaster\Facades\CrudMaster::class
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_crudmaster_table.php.stub';
        $migration->up();
        */
    }


    /**
     * Define database migrations.
     * To run migrations that are only used for testing purposes
     * and not part of your package,
     *
     * @return void
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(
            workbench_path('database/migrations')
        );
    }

}
