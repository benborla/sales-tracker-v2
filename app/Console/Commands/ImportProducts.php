<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Excel;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sales Tracker Import Product Data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->output->title('Starting import');
        (new ProductsImport)->withOutput($this->output)->import('import-products.xlsx');
        $this->output->success('Import successful');
    }
}
