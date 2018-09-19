<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Traits\CreateEmailTraits;
use App\Helpers\UtilDebug;

class CreateEmail extends Command
{
    use CreateEmailTraits;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:email';

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
     * @return mixed
     */
    public function handle()
    {
        UtilDebug::debug("start processing");
        $response = $this->createEmail();
        UtilDebug::print_r_array('Response',$response);
        UtilDebug::debug("End processing");
    }
}
