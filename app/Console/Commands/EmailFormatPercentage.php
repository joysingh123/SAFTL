<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\EmailFormat;
use App\Helpers\UtilDebug;

class EmailFormatPercentage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'percentage:emailformat';

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
//        $limit = 1500;
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $email_format = EmailFormat::all();
        foreach ($email_format AS $ef){
            $format_count = $ef->format_count;
            $available_email_count = $ef->available_email_count;
            if($format_count > 0 && $available_email_count > 0){
                $percentage = ($format_count/$available_email_count)*100;
                $ef->format_percentage = ceil($percentage);
                $ef->save();
            }
        }
        UtilDebug::debug("End processing");
    }
}
