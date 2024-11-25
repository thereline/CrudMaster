<?php

namespace Thereline\CrudMaster;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Thereline\CrudMaster\Commands\CrudMasterCommand;
use Thereline\CrudMaster\Commands\CrudMasterControllerCommand;
use Thereline\CrudMaster\Commands\CrudMasterCrudViewsCommand;
use Thereline\CrudMaster\Commands\CrudMasterModelCommand;
use Thereline\CrudMaster\Commands\CrudMasterRoutesCommand;
use Thereline\CrudMaster\Commands\CrudMasterServiceCommand;
use Thereline\CrudMaster\Contracts\ActionContracts\CrudMasterActionServiceContract;
use Thereline\CrudMaster\Contracts\DataServiceContracts\CrudMasterDataServiceContract;
use Thereline\CrudMaster\Contracts\HttpContracts\CrudMasterHttpServiceContract;
use Thereline\CrudMaster\Services\ActionServices\CrudMasterActionService;
use Thereline\CrudMaster\Services\DataServices\CrudMasterDataService;
use Thereline\CrudMaster\Services\HttpServices\CrudMasterHttpService;

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
            ->hasTranslations()
            //->hasAssets()
            ->hasMigration('create_crudmaster_table')
            ->hasCommands(
                CrudMasterCommand::class,
                CrudMasterCrudViewsCommand::class,
                CrudMasterRoutesCommand::class,
                CrudMasterControllerCommand::class,
                CrudMasterModelCommand::class,
                CrudMasterServiceCommand::class
            );
    }

    public function registeringPackage(): void
    {

        $this->publishes([
            __DIR__.'/../package.json' => base_path('package.json'),
        ], 'crudmaster-assets');

        $this->app->bind(
            CrudMasterHttpServiceContract::class,
            CrudMasterHttpService::class
        );

        // Register the  base data service
        $this->app->bind(
            CrudMasterDataServiceContract::class,
            CrudMasterDataService::class
        );

        $this->app->bind(
            CrudMasterActionServiceContract::class,
            CrudMasterActionService::class
        );

        // Register the command
        if ($this->app->runningInConsole()) {
            $this->commands([
                //GenerateCrud::class,
            ]);
        }
    }
}
