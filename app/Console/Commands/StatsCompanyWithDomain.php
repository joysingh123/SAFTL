<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use DB;
use App\CompaniesWithDomain;

class StatsCompanyWithDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:companystats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update companies with domain stats';

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
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $available_email = DB::table('available_email')
                ->select(DB::raw("company_domain,count(*) AS available_email_count"))
                ->where('first_name', '!=', "FIRSTNAME")
                ->groupBy('company_domain')
                ->get();
        $bounce_email = DB::table('bounce_email')
                ->select(DB::raw("SUBSTRING_INDEX(email,'@',-1) AS domain, count(SUBSTRING_INDEX(email,'@',-1)) AS bounce_email_count"))
                ->groupBy('domain')
                ->get();
        
        foreach ($available_email AS $ae){
            $domain = $ae->company_domain;
            $available_email_count = $ae->available_email_count;
            CompaniesWithDomain::where('company_domain',trim($domain))->update(["available_email_count"=>$available_email_count]);
        }
        
        foreach ($bounce_email AS $be){
            $domain = $be->domain;
            $bounce_email_count = $be->bounce_email_count;
            CompaniesWithDomain::where('company_domain',trim($domain))->update(["bounce_email_count"=>$bounce_email_count]);
        }
        
        UtilDebug::debug("End processing");
    }
}
