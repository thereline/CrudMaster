<?php

namespace Thereline\CrudMaster\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudMasterCrudViewsCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'crudmaster:generate-views {name} {--blade} {--vue} {--tailwind} {--custom}';

    protected $description = 'Generate CRUD views for a given resource';

    public function handle(): void
    {
        $name = $this->argument('name');
        $useBlade = $this->option('blade');
        $useVue = $this->option('vue');
        $useTailwind = $this->option('tailwind');
        $useCustom = $this->option('custom');

        if ($useBlade) {
            $this->generateBladeViews($name, $useTailwind, $useCustom);
        }

        if ($useVue) {
            $this->generateVueViews($name, $useTailwind, $useCustom);
        }

    }

    protected function generateBladeViews($name, $useTailwind, $useCustom): void
    {
        $stubsDir = __DIR__.'/Stubs/Views/blade';
        //$name =  Str::camel($name);
        $viewsDir = resource_path("views/{$name}");

        $files = ['index.blade.php', 'create.blade.php', 'show.blade.php', 'edit.blade.php'];
        $this->viewWriter($name, $files, $stubsDir, $viewsDir, $useTailwind, $useCustom);

        $this->info('Blade Views generated successfully.');

    }

    protected function generateVueViews($name, $useTailwind, $useCustom): void
    {
        $stubsDir = __DIR__.'/Stubs/Views/vue'; //base_path('Stubs/blade');
        $viewsDir = resource_path("js/Pages/{$name}");

        // Define the destination directory in the Laravel application
        //$destinationDirectory = resource_path('js/Components');
        //$folders = ['Table', 'Form', 'Icons'];
        //$this->componentsWriter($stubsDir, $destinationDirectory, $folders);

        $files = ['Index.vue', 'Create.vue', 'Show.vue', 'Edit.vue'];
        $this->viewWriter($name, $files, $stubsDir, $viewsDir, $useTailwind, $useCustom);

        $this->info('Vue Views generated successfully.');

    }

    public function viewWriter(string $name, array $views, string $stubsDir, string $viewsDir, $useTailwind, $useCustom): void
    {

        //Create vie directory if not exiting
        if (! File::exists($viewsDir)) {
            File::makeDirectory($viewsDir, 0755, true);
        }

        foreach ($views as $file) {
            $stubPath = "{$stubsDir}/{$file}";
            $viewPath = "{$viewsDir}/{$file}";
            $content = File::get($stubPath);
            $content = str_replace('{{resource}}', Str::studly($name), $content);

            if ($useTailwind) {
                //$tailwindPath = File::get(__DIR__ . '/Stubs/Views/common/tailwind.css');
                //$tailwindContent = File::get($tailwindPath);
                $tailwindContent = File::get(__DIR__.'/Stubs/Views/common/tailwind.css');

                $content = str_replace('/* Add custom styles or import Tailwind CSS */', $tailwindContent, $content);
            } elseif ($useCustom) {
                $customPath = File::get(__DIR__.'/Stubs/Views/common/custom.css');
                $customContent = File::get($customPath);
                $content = str_replace('/* Add custom styles or import Tailwind CSS */', $customContent, $content);
            }

            File::put($viewPath, $content);
        }
    }

    /*public function componentsWriter(string $sourceDir, string $destinationDir, array $components): void
    {

        // Check if destination directory exists, and create it if necessary
        if (! File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        foreach ($components as $folder) {

            $stubPath = "{$sourceDir}/{$folder}";
            $componentPath = "{$destinationDir}/{$folder}";

            // Check if the source directory exists
            if (File::exists($stubPath)) {

                if (! File::exists($componentPath)) {
                    File::makeDirectory($componentPath, 0755, true);
                }

                // Copy the directory and its contents
                File::copyDirectory($stubPath, $componentPath);
                $this->info("Component{$folder} created ");
            } else {
                $this->warn("Source directory not found: {$folder}");
            }
        }

    }*/
}
