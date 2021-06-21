<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RollbackCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback all auto generated codes';

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
        if (count($tables) == 1) {
            $this->alert('Please Rollback your code first, then the migrates ! ');
            $this->info('You have rollback your DB, please migrate then run this command | e.g: php artisan migrate-');
        } else {
            $this->info('Rollback:');
            foreach ($tables as $stt => $item) {
                $table_name    = head($item);
                $exclude_table = [
                    'migrations',
                    'failed_jobs',
                    'password_resets'
                ];

                if ( ! in_array($table_name, $exclude_table)) {
                    // turn table_name into ModelNames
                    $model = Str::singular($table_name);
                    $model = ucwords($model, '_');
                    $model = str_replace('_', '', $model);

                    Artisan::call('infyom:rollback ' . $model . ' api');
                    $this->info("\t" . ($stt) . ": " . $model);
                }
            }
            $this->alert('Please migrate:rollback then remove all migrate files that auto generated ! ');
        }
    }
}
