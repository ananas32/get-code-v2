<?php

namespace App\Console\Commands;

use App\Imports\GetCodeCsv;
use Illuminate\Console\Command;
use Excel;

class MakeParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        \App\Jobs\ImportCsv::dispatch();

//        Excel::import(new GetCodeCsv, storage_path(). '/app/getcode.csv');
    }
}
