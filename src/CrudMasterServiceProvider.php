<?php

namespace Thereline\CrudMaster;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Thereline\CrudMaster\Commands\CrudMasterCommand;

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
}
