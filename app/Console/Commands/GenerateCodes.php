<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Turn tables in database into API resources';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tables = DB::select('SHOW TABLES');
        $this->info('Created:');

        foreach ($tables as $stt => $item) {
            $table_name    = head($item);
            $exclude_table = [
                'migrations',
                'failed_jobs',
                'password_resets'
            ];

            if ( ! in_array($table_name, $exclude_table)) {
                // turn table_name into ModelNamesF
                $model = Str::singular($table_name);
                $model = ucwords($model, '_');
                $model = str_replace('_', '', $model);

                Artisan::call('infyom:api ' . $model
                              . ' --fromTable --tableName=' . $table_name
                              . ' --no-interaction');
                $this->info("\t" . ($stt) . ": " . $model);
            }
        }
        Artisan::call('migrate:generate --no-interaction');
    }
}
