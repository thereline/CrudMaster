<?php

namespace Thereline\CrudMaster\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CrudMasterCommand extends Command
{

    protected $signature = 'crudmaster:generate {model} {--columns=} {--views=blade}';

    protected $description = 'Generate CRUD for a model with specified columns';

    public function handle(): int
    {
        $model = $this->argument('model');
        $columns = $this->option('columns');
        $views = $this->option('views');

        $this->generateModel($model, $columns);

        $this->comment('All done');

        return self::SUCCESS;
    }

    private function generateModel(string $model, string $columns): void
    {
        $columnsArray = explode(',', $columns);
        $fillable = implode("', '", $columnsArray);

        // Generate the model with migration and fillable columns
        Artisan::call('make:model', [
            'name' => $model,
            '--migration' => true,
            '--fillable' => $fillable
        ]);

        $this->info('Model created successfully.');
    }





}
