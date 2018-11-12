<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\CompanyMaster;
use App\Companies;

class PopulateSalesbotCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:salesbotcompanies';

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
        UtilDebug::debug("Start Processing");
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $limit = 1000;
        $company_master = CompanyMaster::where('Status','not processed')->take($limit)->get();
        if($company_master->count() > 0){
            foreach($company_master AS $cm){
                $company_name = trim($cm->company_name);
                $website = trim($cm->website);
                $linkedin_url = trim($cm->linkedin_URL);
                $industry = trim($cm->industry);
                $country = trim($cm->country);
                $domain = trim($cm->domain);
                $postal_code = trim($cm->postal_code);
                $companies = Companies::where('domain',$domain)->get();
                if($companies->count() > 0){
                    $cm->status = "already exist";
                    $cm->save();
                }else{
                    $salebot_company = new Companies();
                    $salebot_company->company = $company_name;
                    $salebot_company->companyWebsite = $website;
                    $salebot_company->industry = $industry;
                    $salebot_company->country_id = $country;
                    $salebot_company->zipcode = $postal_code;
                    $salebot_company->domain = $domain;
                    $save_as = $salebot_company->save();
                    if($save_as){
                        $id = $salebot_company->id;
                        $cm->reference_id = $id;
                        $cm->status = "processed";
                        $cm->save();
                    }
                }
            }
        }else{
            echo "No, Data Found For Procesing";
        }
        UtilDebug::debug("End Processing");
    }
}
