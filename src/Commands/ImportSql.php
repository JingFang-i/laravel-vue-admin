<?php


namespace Jmhc\Admin\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导入sql';


    public function handle()
    {
        $this->import();
        $this->info('成功');
    }

    protected function import()
    {
        foreach ($this->getSql() as $sqlPath) {
            $sql = file_get_contents($sqlPath);
            DB::unprepared($sql);
        }
    }

    protected function getSql(): array
    {
        $paths = [];
        foreach (config('admin.import_table') as $name){
            $paths[] = __DIR__ . '/../../database/' . $name . '.sql';
        }
        return $paths;
    }
}
