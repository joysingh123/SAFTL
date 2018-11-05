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
        
        //Step 1
        
        DB::statement("UPDATE companies_with_domain A,companies_with_domain B SET A.website = B.company_domain WHERE A.id = B.id");
        
        //Step 2
        $contacts_domain = DB::table('contacts')->select(DB::raw("domain,count(*) AS total_record"))->groupBy('domain')->get();
        $contacts_domain_valid_stats = DB::table('contacts')->select(DB::raw("domain,email_status,count(email_status) AS count"))->groupBy('domain')->groupBy('email_status')->get();
        
        foreach ($contacts_domain AS $cd){
            $domain = $cd->domain;
            $total_record = $cd->total_record;
            $contacts_valid_record = $contacts_domain_valid_stats->where("domain","=",$domain);
            $contacts_valid_record = $contacts_valid_record->all();
            $valid = NULL;
            $catch_all = NULL;
            $invalid = NULL;
            if(count($contacts_valid_record) > 0){
                foreach ($contacts_valid_record AS $cvr){
                    if($cvr->email_status == "valid"){
                       $valid = $cvr->count; 
                    }
                    if($cvr->email_status == "invalid"){
                       $invalid = $cvr->count; 
                    }
                    if($cvr->email_status == "catch all"){
                       $catch_all = $cvr->count; 
                    }
                }
            }
            CompaniesWithDomain::where('company_domain',trim($domain))->update(["total_record"=>$total_record,"valid"=>$valid,"invalid"=>$invalid,"catch_all"=>$catch_all]);
        }
        
        //Step 3
        $email_validation_domian_count = DB::table('email_validation')
                ->select(DB::raw("SUBSTRING_INDEX(email,'@',-1) AS domain,count(SUBSTRING_INDEX(email,'@',-1)) AS total_count"))
                ->groupBy('domain')
                ->get();
        $email_validation = DB::table('email_validation')
                ->select(DB::raw("SUBSTRING_INDEX(email,'@',-1) AS domain,mx_found,count(mx_found) AS count_mx_found"))
                ->groupBy('domain')
                ->groupBy('mx_found')
                ->get();
        foreach($email_validation_domian_count AS $ev){
            $domain = trim($ev->domain);
            $email_validation_filter = $email_validation->where("domain","=",$domain);
            $email_validation_filter = $email_validation_filter->all();
            $mx_found_data = FALSE;
            if(count($email_validation_filter) > 0){
                if(count($email_validation_filter) == 1){
                    foreach($email_validation_filter AS $ets){
                        if($ets->mx_found == "true"){
                           $mx_found_data = TRUE; 
                        }
                        if($ets->mx_found == "false"){
                           $mx_found_data = FALSE; 
                        }
                    }
                }
                if(count($email_validation_filter) > 1){
                    $mx_found_data = FALSE;
                }
                CompaniesWithDomain::where('company_domain',trim($domain))->update(["mx_record"=>$mx_found_data]);
            }
        }
        
        $available_email = DB::table('available_email')
                ->select(DB::raw("company_domain,count(*) AS available_email_count"))
                ->where('first_name', '!=', "FIRSTNAME")
                ->groupBy('company_domain')
                ->get();
        foreach ($available_email AS $ae){
            $domain = $ae->company_domain;
            $available_email_count = $ae->available_email_count;
            CompaniesWithDomain::where('company_domain',trim($domain))->update(["available_email_count"=>$available_email_count]);
        }
        
        //Step 5
        $bounce_email = DB::table('bounce_email')
                ->select(DB::raw("SUBSTRING_INDEX(email,'@',-1) AS domain, count(SUBSTRING_INDEX(email,'@',-1)) AS bounce_email_count"))
                ->groupBy('domain')
                ->get();
        foreach ($bounce_email AS $be){
            $domain = $be->domain;
            $bounce_email_count = $be->bounce_email_count;
            CompaniesWithDomain::where('company_domain',trim($domain))->update(["bounce_email_count"=>$bounce_email_count]);
        }
        UtilDebug::debug("End processing");
    }
}
