<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Emails;

class RemoveApiValidEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:apivalidemail';

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
        $emails = Emails::whereIn('status',['invalid','valid','catch all'])->get();
        UtilDebug::print_message("totol deletion", $emails->count());
        if($emails->count() > 0){
            foreach($emails AS $email){
                $email->delete();
            }
        }
        UtilDebug::debug("end processing");
    }
}
