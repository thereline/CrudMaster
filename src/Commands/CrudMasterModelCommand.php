<?php

namespace Thereline\CrudMaster\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudMasterModelCommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'crudmaster:generate-model
                            {resource : The name of the resource (e.g., School)}
                            {--migration : Generate a migration for the resource}
                            {--factory : Generate a factory for the resource}
                            {--seeder : Generate a seeder for the resource}
                            {--columns= : Comma-separated list of columns (e.g., name:string,email:string)}';

    protected $description = 'Generate a Model for a resource with optional migration, factory, and seeder';

    public function handle(): void
    {
        $resource = $this->argument('resource');
        $resourceStudly = Str::studly($resource);
        $resourceLower = Str::lower($resource);
        $columns = $this->option('columns');

        $this->generateModel($resourceStudly, $resourceLower, $columns);

        if ($this->option('migration')) {
            $this->generateMigration($resourceLower, $columns);
        }

        if ($this->option('factory')) {
            $this->generateFactory($resourceStudly, $columns);
        }

        if ($this->option('seeder')) {
            $this->generateSeeder($resourceStudly, $resourceLower, $columns);
        }

        $this->info("Model for {$resourceStudly} generated successfully.");
    }

    protected function generateModel($resourceStudly, $resourceLower, $columns): void
    {
        $stubPath = __DIR__.'/Stubs/resource_model.stub';
        $stubContent = File::get($stubPath);
        $fillable = $this->parseFillable($columns);
        $stubContent = str_replace(
            ['{{resource}}', '{{resource_lower}}', '{{ fillable }}'],
            [$resourceStudly, $resourceLower, $fillable],
            $stubContent
        );

        $modelPath = app_path("Models/{$resourceStudly}.php");

        File::ensureDirectoryExists(dirname($modelPath));
        File::put($modelPath, $stubContent);
    }

    protected function generateMigration($resourceLower, $columns): void
    {
        $migrationName = "create_{$resourceLower}_table";
        $migrationPath = database_path('migrations/'.date('Y_m_d_His')."_{$migrationName}.php");
        $stubPath = __DIR__.'/Stubs/resource_migration.stub';
        $stubContent = File::get($stubPath);
        $columnsMigration = $this->parseColumnsForMigration($columns);
        $stubContent = str_replace(
            ['{{ table }}', '{{ columns }}'],
            [Str::plural($resourceLower), $columnsMigration],
            $stubContent
        );

        File::ensureDirectoryExists(dirname($migrationPath));
        File::put($migrationPath, $stubContent);
    }

    protected function generateFactory($resourceStudly, $columns): void
    {
        $stubPath = __DIR__.'/Stubs/resource_factory.stub';
        $stubContent = File::get($stubPath);
        $columnsFactory = $this->parseColumnsForFactory($columns);
        $stubContent = str_replace(
            ['{{ resource }}', '{{ columns }}'],
            [$resourceStudly, $columnsFactory],
            $stubContent
        );

        $factoryPath = database_path("factories/{$resourceStudly}Factory.php");

        File::ensureDirectoryExists(dirname($factoryPath));
        File::put($factoryPath, $stubContent);
    }

    protected function generateSeeder($resourceStudly, $resourceLower, $columns): void
    {
        $stubPath = __DIR__.'/Stubs/resource_seeder.stub';
        $stubContent = File::get($stubPath);
        $columnsSeeder = $this->parseColumnsForSeeder($columns);
        $stubContent = str_replace(
            ['{{ table }}', '{{ resource }}', '{{ columns }}'],
            [Str::plural($resourceLower), $resourceStudly, $columnsSeeder],
            $stubContent
        );

        $seederPath = database_path("seeders/{$resourceStudly}Seeder.php");

        File::ensureDirectoryExists(dirname($seederPath));
        File::put($seederPath, $stubContent);
    }

    protected function parseFillable(array | string $columns): string
    {
        if (empty($columns)) {
            return '';
        }

             // Decode JSON string if provided.
        if (is_string($columns)) {
            $decoded = json_decode($columns, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $columns = $decoded;
            } else {
                // If not JSON, assume it's a comma-separated string.
                $columns = explode(',', $columns);
            }
        }

        // Handle array of objects or key-value pairs.
        $fillable = array_map(function ($column) {
            if (is_array($column) && isset($column['name'])) {
                return "'" . $column['name'] . "'";
            }
            return "'" . $column . "'";
        }, $columns);


        return implode(', ', $fillable);
    }

    protected function parseColumnsForMigration(array|string $columns): string
    {
        if (empty($columns)) {
            return '';
        }

        if (is_string($columns)) {
            $decoded = json_decode($columns, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $columns = $decoded;
            } else {
                throw new InvalidArgumentException("Invalid JSON format provided.");
            }
        }

        $migrationColumns = array_map(function ($column) {
            if (is_array($column) && isset($column['name'], $column['type'])) {
                $migrationLine = "\$table->{$column['type']}('{$column['name']}')";

                if (isset($column['nullable']) && $column['nullable'] === true) {
                    $migrationLine .= "->nullable()";
                }

                if (isset($column['default'])) {
                    $migrationLine .= "->default('{$column['default']}')";
                }

                return $migrationLine . ';';
            }

            throw new InvalidArgumentException("Each column must have a 'name' and 'type'.");
        }, $columns);

        return  "\n    " . implode("\n    ", $migrationColumns) . "\n";
    }

    protected function parseColumnsForFactory(array|string $columns): string
    {
        if (empty($columns)) {
            return '';
        }

        // Decode JSON string if provided.
        if (is_string($columns)) {
            $decoded = json_decode($columns, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $columns = $decoded;
            } else {
                throw new InvalidArgumentException("Invalid JSON format provided.");
            }
        }

        // Map columns to factory syntax.
        $factoryColumns = array_map(function ($column) {
            if (is_array($column) && isset($column['name'], $column['type'])) {
                $faker = match ($column['type']) {
                    'string' => "\$this->faker->word",
                    'integer' => "\$this->faker->numberBetween(1, 100)",
                    'boolean' => "\$this->faker->boolean",
                    'text' => "\$this->faker->paragraph",
                    'date' => "\$this->faker->date()",
                    'timestamp' => "\$this->faker->dateTime()",
                    default => "\$this->faker->word", // Fallback for unknown types.
                };

                return "'{$column['name']}' => {$faker}";
            }

            throw new InvalidArgumentException("Each column must have a 'name' and 'type'.");
        }, $columns);

        return "[\n    " . implode(",\n    ", $factoryColumns) . "\n]";
    }

    protected function parseColumnsForSeeder(array|string $columns): string
    {
        if (empty($columns)) {
            return '';
        }

        // Decode JSON string if provided.
        if (is_string($columns)) {
            $decoded = json_decode($columns, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $columns = $decoded;
            } else {
                throw new InvalidArgumentException("Invalid JSON format provided.");
            }
        }        

        // Map columns to seeder syntax.
        $seederColumns = array_map(function ($column) {
            if (is_array($column) && isset($column['name'], $column['type'])) {
                $faker = match ($column['type']) {
                    'string' => "\$this->faker->word",
                    'integer' => "\$this->faker->numberBetween(1, 100)",
                    'boolean' => "\$this->faker->boolean",
                    'text' => "\$this->faker->paragraph",
                    'date' => "\$this->faker->date()",
                    'timestamp' => "\$this->faker->dateTime()",
                    default => "\$this->faker->word", // Fallback for unknown types.
                };

                return "'{$column['name']}' => {$faker}";
            }

            throw new InvalidArgumentException("Each column must have a 'name' and 'type'.");
        }, $columns);

        // Return as an array of seedable values for insertion.
        return "[\n    " . implode(",\n    ", $seederColumns) . "\n]";
    }






}
