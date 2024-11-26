<?php

namespace Thereline\CrudMaster\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudMasterRoutesCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'crudmaster:generate-routes
                            {resource : The name of the resource (e.g., School)}
                            {--merge : Whether to merge routes into existing files}
                            {--type=both : Type of routes to generate (api, web, both)}';

    protected $description = 'Generate routes for a resource';

    public function handle(): void
    {
        $resource = $this->argument('resource');
        $merge = $this->option('merge');
        $type = $this->option('type');

        $resourceStudly = Str::studly($resource);
        $resourceLower = Str::lower($resource);

        if ($type === 'both' || $type === 'web') {
            $this->generateRoutes('web', $resourceStudly, $resourceLower, $merge);
        }

        if ($type === 'both' || $type === 'api') {
            $this->generateRoutes('api', $resourceStudly, $resourceLower, $merge);
        }

        $this->info("Routes for {$resourceStudly} generated successfully.");
    }

    protected function generateRoutes($type, $resourceStudly, $resourceLower, $merge): void
    {
        //$stubPath = File::get(__DIR__."/Stubs/Routes/{$type}.stub") ;
        $stubContent = File::get(__DIR__."/Stubs/Routes/{$type}.stub");
        $stubContent = str_replace(
            ['{{resource}}', '{{resource_lower}}'],
            [$resourceStudly, Str::plural($resourceLower)],
            $stubContent
        );

        $stubCommonContent = File::get(__DIR__."/Stubs/Routes/common/{$type}.stub");
        $stubCommonContent = str_replace(
            ['{{resource}}', '{{resource_lower}}'],
            [$resourceStudly, Str::plural($resourceLower)],
            $stubCommonContent
        );

        if ($merge) {
            $this->mergeRoutes($type, $stubContent, $stubCommonContent);
        } else {
            $this->createRouteFile($type, $resourceLower, $stubContent);
        }
    }

    protected function mergeRoutes($type, $stubContent, $stubCommonContent): void
    {
        $routeFile = base_path("routes/{$type}.php");

        //$webRoutesPath = base_path('routes/web.php');
        //$apiRoutesPath = base_path('routes/api.php');

        // Ensure routes/web.php exists
        if (! File::exists($routeFile)) {
            File::put($routeFile, $stubContent);
        }
        $existingContent = File::get($routeFile);

        if (! str_contains($existingContent, $stubCommonContent)) {

            File::append($routeFile, PHP_EOL.PHP_EOL.$stubCommonContent);
            $this->info("Routes merged into routes/{$type}.php.");
        } else {
            $this->info("Routes already exist in routes/{$type}.php.");
        }
    }

    protected function createRouteFile($type, $resourceLower, $stubContent): void
    {
        $routeDir = base_path("routes/{$type}");
        if (! File::exists($routeDir)) {
            File::makeDirectory($routeDir, 0755, true);
        }

        $routeFile = "{$routeDir}/{$resourceLower}.php";
        File::put($routeFile, $stubContent);
        $this->info("Routes created at routes/{$type}/{$resourceLower}.php.");
    }
}
