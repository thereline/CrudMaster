<?php

namespace Thereline\CrudMaster\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CrudMasterPackageJsonCommand extends Command
{
    protected $signature = 'crudmaster:merge-package-json';

    protected $description = 'Merge your package.json entries into the application\'s package.json';

    public function handle(): void
    {
        $packageJsonPath = base_path('package.json');
        $packageJsonContent = json_decode(File::get($packageJsonPath), true);
        $yourPackageJsonPath = __DIR__.'/../../package.json';
        $yourPackageJsonContent = json_decode(File::get($yourPackageJsonPath), true);

        $mergedContent = array_merge_recursive($packageJsonContent, $yourPackageJsonContent);

        File::put($packageJsonPath, json_encode($mergedContent, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $this->info('Merged package.json entries successfully.');
    }
}
