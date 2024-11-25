<?php

namespace Thereline\CrudMaster\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudMasterControllerCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'crudmaster:generate-controller
                            {resource : The name of the resource (e.g., School)}
                            {serviceKind : The kind of controller service to use (simple or standard)}';

    protected $description = 'Generate a Resource Controller';

    public function handle(): void
    {
        $resource = $this->argument('resource');
        $serviceKind = $this->argument('serviceKind');

        $resourceStudly = Str::studly($resource);
        $resourceLower = Str::lower($resource);

        $stubPath = match ($serviceKind) {
            'simple' => __DIR__.'/Stubs/resource_serviceless_controller.stub',
            'standard' => __DIR__.'/Stubs/resource_service_controller.stub',
            default => '',
        };

        /* $stubPath = $useService
             ? __DIR__ . '/Stubs/resource_service_controller.stub'
             : __DIR__ . '/Stubs/resource_serviceless_controller.stub';*/

        $stubContent = File::get($stubPath);
        $stubContent = str_replace(
            ['{{resource}}', '{{resource_lower}}'],
            [$resourceStudly, $resourceLower],
            $stubContent
        );

        $controllerPath = app_path("Http/Controllers/{$resourceStudly}Controller.php");

        File::ensureDirectoryExists(dirname($controllerPath));
        File::put($controllerPath, $stubContent);

        $this->info("Controller for {$resourceStudly} generated successfully.");
    }
}
