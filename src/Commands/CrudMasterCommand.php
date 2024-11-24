<?php

namespace Thereline\CrudMaster\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Laravel\Prompts;
use Thereline\CrudMaster\CrudMaster;

class CrudMasterCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'crudmaster:generate {--N|name=}';

    protected $description = 'Generate CRUD files (model, controller, service, routes, views, migration, and factory) for a given resource';

    public function handle(): void
    {
        $resourceName = $this->getResourceName();
        $modelName = CrudMaster::stringToPascalCase($resourceName);
        $this->info("Generating CRUD for: {$modelName}");

        $configSource = $this->getConfigSource();

        $configValues = $configSource === 'input' ? $this->getInputConfig() : $this->getFileConfig();

        // Extract configuration values
        extract($configValues);

        $this->generateCrudFiles($modelName, $configValues);
    }

    protected function getResourceName(): string
    {

        return
            $this->option('name') ?:
                Prompts\text(
                    label: 'What is the name of the resource?',
                    required: true,
                    hint: 'This will be used for all class names related to the resource',
                    transform: fn (string $value) => trim($value),
                );

    }

    protected function getConfigSource(): string
    {
        return Prompts\select(
            label: 'What configuration should be used?',
            options: [
                'input' => 'Input Configuration',
                'file' => 'File Configuration',
            ],
            default: 'input'
        );
    }

    protected function getInputConfig(): array
    {
        return Prompts\form()
            ->select(
                label: 'Which design structure should be used?',
                options: ['simple' => 'Without Resource Service', 'standard' => 'With Resource Service'],//$this->getGenerationOptions(),
                default: 'standard',
                name: 'serviceKind'
            )
            ->confirm(label: 'With migration?', name: 'migration')
            ->confirm(label: 'With factory?', name: 'factory')
            ->confirm(label: 'With seeder?', name: 'seeder')
            ->select(
                label: 'Which routes should be generated?',
                options: ['api=>API routes only', 'web' => 'Web routes only', 'both ' => 'Both API and Web routes', 'none' => 'No routes'],
                default: 'both ',
                required: true,
                name: 'routes'
            )
            ->select(
                label: 'Where should the route be placed?',
                options: ['merge' => 'Merge with web and API file', 'separate' => 'Separate resource route file'],
                default: 'merge',
                name: 'routeStructure'
            )
            ->select(
                label: 'Which view should be generated?',
                options: ['vue' => 'Vue with Inertia', 'blade' => 'Blade plane', 'none' => 'No Vue'],
                default: 'vue',
                name: 'views'
            )
            ->submit();
    }

    protected function getFileConfig(): array
    {
        try {
            $config = config('crudmaster.generate');

            return [
                'serviceKind' => 'standard',  // Default option if not specified in file
                'migration' => $config['migration'] ?? true,
                'factory' => $config['factory'] ?? true,
                'seeder' => $config['seeder'] ?? true,
                'routes' => $config['routes']['type'] ?? 'both',
                'views' => $config['view'] ?? 'vue',
                'routeStructure' => $config['routes']['structure'] ?? 'merge', // $config->get('routes.structure', 'Merge')
            ];
        } catch (\Exception $e) {
            $this->error('Error in configuration file: '.$e->getMessage());

            return [];
        }
    }

    protected function getGenerationOptions(): array
    {
        return [
            'simple' => 'Simple => Only Controller',
            'standard' => 'Standard => Model, Service, Controller',
            'advanced' => 'Advanced => Model,DataService, ActionServices, Service, Controller',
            'expert' => 'Expert => Interfaces Only',
        ];
    }

    protected function generateCrudFiles(string $modelName, array $configValues): void
    {
        $bar = $this->output->createProgressBar(100);
        $bar->start();
        $this->newLine();

        $this->generateFile('model', $modelName, $configValues);
        $this->newLine();
        $bar->advance(20);
        $this->generateFile('service', $modelName, $configValues);
        $this->newLine();
        $bar->advance(20);
        $this->generateFile('controller', $modelName, $configValues);
        $this->newLine();
        $bar->advance(20);
        $this->generateRoutes($modelName, $configValues);
        $this->newLine();
        $bar->advance(20);
        $this->generateViews($modelName, $configValues);
        $this->newLine();
        $bar->advance(20);

        $bar->finish();
        $this->newLine();
        $this->info('All done!');
    }

    protected function generateFile(string $fileType, string $modelName, array $configValues): void
    {
        $additionalArgs = $this->getAdditionalArgs($fileType, $configValues);

        if ($additionalArgs !== null) {
            $this->call(
                "crudmaster:generate-{$fileType}",
                array_merge(['resource' => $modelName], $additionalArgs));
        } else {
            $this->call("crudmaster:generate-{$fileType}", ['resource' => $modelName]);
        }
    }

    protected function getAdditionalArgs(string $fileType, array $configValues): ?array
    {

        return match ($fileType) {
            'controller' => ['serviceKind' => $configValues['serviceKind']],
            //'service' => null,//['--data-service' => $configValues['dataService'] ?? false],
            'model' => [
                '--migration' => $configValues['migration'] ?? false,
                '--factory' => $configValues['factory'] ?? false,
                '--seeder' => $configValues['seeder'] ?? false,
                '--columns' => $this->getModelColumns() ?? false,
            ],
            default => null,
        };
    }

    protected function getModelColumns(): ?array
    {
        $columns = [];

        $columnCount = Prompts\text(
            label: 'How many columns do you want to add to the model?',
            required: true,
            validate: fn (string $value) => is_numeric($value) && (int) $value >= 0 ?
                null :
                'Please enter a valid number greater or equal to 0',
            hint: 'Use 0 if no column is needed.'
        );

        if ($columnCount > 0) {
            for ($i = 1; $i <= (int) $columnCount; $i++) {
                $columnName = Prompts\text(
                    label: "Enter the name for column {$i}",
                    required: true
                );

                $columnType = Prompts\select(
                    label: "Select the type for column {$i}",
                    options: ['string', 'integer', 'boolean', 'text', 'date', 'timestamp'],
                    required: true
                );

                $columns[] = [
                    'name' => $this->stringToPascalCase($columnName),
                    'type' => $columnType,
                ];
            }
            $this->info('Columns: '.json_encode($columns));

            return $columns;
        }

        return null;

    }

    protected function stringToPascalCase(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $string)));
    }

    protected function generateRoutes(string $modelName, array $configValues): void
    {
        $route = $configValues['routes'] ?? null;

        $this->call('crudmaster:generate-routes', [
            'resource' => $modelName,
            '--merge' => $configValues['routeStructure'],
            '--type' => strtolower($route),
        ]);

    }

    protected function generateViews(string $modelName, array $configValues): void
    {
        $view = $configValues['views'] ?? null;
        if ($view === 'blade') {
            $this->call('crudmaster:generate-views', [
                'name' => $modelName,
                '--blade' => true,
                '--tailwind' => true,
            ]);
        }
        if ($view === 'vue') {
            $this->call('crudmaster:generate-views', [
                'name' => $modelName,
                '--vue' => true,
                '--tailwind' => true,
            ]);
        }

    }
}
