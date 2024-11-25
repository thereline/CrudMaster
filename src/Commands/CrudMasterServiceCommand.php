<?php

namespace Thereline\CrudMaster\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudMasterServiceCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'crudmaster:generate-service
                            {resource : The name of the resource (e.g., School)}';

    protected $description = 'Generate a Service for a resource and register it in the CrudMasterResourceServiceProvider';

    public function handle(): void
    {
        $resource = $this->argument('resource');
        $resourceStudly = Str::studly($resource);
        $resourceLower = Str::lower($resource);

        $this->generateService($resourceStudly, $resourceLower);
        $this->registerService($resourceStudly, $resourceLower);

        $this->info("Service for {$resourceStudly} generated and registered successfully.");
    }

    protected function generateService($resourceStudly, $resourceLower): void
    {
        $stubPath = __DIR__.'/Stubs/resource_service.stub';
        $stubContent = File::get($stubPath);
        $stubContent = str_replace(
            ['{{ resource }}', '{{ resource_lower }}'],
            [$resourceStudly, $resourceLower],
            $stubContent
        );

        $servicePath = app_path("Services/{$resourceStudly}Service.php");

        File::ensureDirectoryExists(dirname($servicePath));
        File::put($servicePath, $stubContent);
    }

    protected function registerService($resourceStudly, $resourceLower): void
    {
        $providerPath = app_path('Providers/CrudMasterResourceServiceProvider.php');
        $serviceClass = "App\\Services\\{$resourceStudly}Service";

        if (! File::exists($providerPath)) {
            $this->createProvider($providerPath);
        }

        $providerContent = File::get($providerPath);

        if (! Str::contains($providerContent, $serviceClass)) {
            $bindingCode = "\n\$this->app->bindIf({$resourceStudly}Service::class, function (\$app) {\n"
                ."                return new {$resourceStudly}Service(\n"
                ."                    \$app->make(CrudMasterDataServiceContract::class),\n"
                ."                    \$app->make(CrudMasterActionServiceContract::class),\n"
                ."                    \$app->make(CrudMasterHttpServiceContract::class)\n"
                ."                );\n"
                ."            });\n";

            $pattern = "/public function register\(\)\n\s*{/";
            $replacement = "public function register()\n    {{$bindingCode}";

            $providerContent = preg_replace($pattern, $replacement, $providerContent);

            File::put($providerPath, $providerContent);
        }

        $this->registerProvider();
    }

    protected function createProvider($providerPath): void
    {
        $stubPath = __DIR__.'/Stubs/resource_provider.stub';
        $stubContent = File::get($stubPath);

        File::ensureDirectoryExists(dirname($providerPath));
        File::put($providerPath, $stubContent);

    }

    protected function registerProvider(): void
    {

        $laravelVersion = app()->version();
        $majorVersion = (int) explode('.', $laravelVersion)[0];

        if ($majorVersion >= 11) {
            $this->registerProviderInBootstrap();
        } else {
            $this->registerProviderInConfig();
        }
    }

    protected function registerProviderInConfig(): void
    {
        // Register the provider in the config/app.php
        $appConfigPath = config_path('app.php');
        $appConfigContent = File::get($appConfigPath);

        if (! Str::contains($appConfigContent, 'App\\Providers\\CrudMasterResourceServiceProvider::class')) {
            $pattern = "/'providers' => \[/";
            $replacement = "'providers' => [\n        App\Providers\CrudMasterResourceServiceProvider::class,";
            $appConfigContent = preg_replace($pattern, $replacement, $appConfigContent);
            File::put($appConfigPath, $appConfigContent);
        }
    }

    protected function registerProviderInBootstrap(): void
    {
        $bootstrapProvidersPath = base_path('bootstrap/providers.php');
        $bootstrapProvidersContent = File::get($bootstrapProvidersPath);

        if (! Str::contains($bootstrapProvidersContent, 'App\\Providers\\CrudMasterResourceServiceProvider::class')) {
            $pattern = "return [\n";
            $replacement = "return [\n App\\Providers\\CrudMasterResourceServiceProvider::class, \n ";
            $bootstrapProvidersContent = str_replace($pattern, $replacement, $bootstrapProvidersContent);

            File::put($bootstrapProvidersPath, $bootstrapProvidersContent);
        }
    }
}
