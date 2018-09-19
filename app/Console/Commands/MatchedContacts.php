<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\ContactCompanyMatchTraits;
use App\Helpers\UtilDebug;

class MatchedContacts extends Command
{
    
    use ContactCompanyMatchTraits;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:matchedcontact';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Matched Record From Company Without Domain and contacts';

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
        $response = $this->matchContactCompany();
        UtilDebug::print_r_array('Response',$response);
        UtilDebug::debug("End processing");
    }
}
