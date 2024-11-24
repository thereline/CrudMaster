<?php

namespace Workbench\App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Thereline\CrudMaster\Contracts\ActionContracts\CrudMasterActionServiceContract;
use Thereline\CrudMaster\Contracts\DataServiceContracts\CrudMasterDataServiceContract;
use Thereline\CrudMaster\Contracts\HttpContracts\CrudMasterHttpServiceContract;
use Workbench\App\Contracts\UserServiceContracts\UserActionServiceContract;
use Workbench\App\Services\SchoolService;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->bind(UserActionServiceContract::class, function (Application $app) {
            return new SchoolService(
                $app->make(CrudMasterDataServiceContract::class),
                $app->make(CrudMasterActionServiceContract::class),
                $app->make(CrudMasterHttpServiceContract::class),
            );
        });

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::view('/', 'welcome');
    }
}
