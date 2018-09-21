<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\GenerateEmailFormatTraits;
use App\Helpers\UtilDebug;

class GenerateEmailFormat extends Command
{
    use GenerateEmailFormatTraits;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:emailformat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genearting Email Format For Matched Record';

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
        $response = $this->generateEmailFormat();
        UtilDebug::print_r_array('Response',$response);
        UtilDebug::debug("End processing");
    }
}
