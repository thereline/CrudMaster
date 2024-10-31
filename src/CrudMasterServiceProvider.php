<?php

namespace Thereline\CrudMaster;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Thereline\CrudMaster\Commands\CrudMasterCommand;
use Thereline\CrudMaster\Contracts\BaseRepositoryInterface;
use Thereline\CrudMaster\Repositories\BaseRepository;
use Thereline\CrudMaster\Services\ActionService;
use Thereline\CrudMaster\Services\ActionServiceInterface;

class CrudMasterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('crudmaster')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_crudmaster_table')
            ->hasCommand(CrudMasterCommand::class);
    }


    public function registeringPackage()
    {
        // Register the repository interface and its implementation
        $this->app->bind(
            BaseRepositoryInterface::class,
            BaseRepository::class
        );

        // Register the ActionService
        $this->app->singleton(
            ActionServiceInterface::class, function ($app) {
            return new ActionService($app->make());
        });

        // Register the command
        if ($this->app->runningInConsole()) {
            $this->commands([
                //GenerateCrud::class,
            ]);
        }
    }
}
