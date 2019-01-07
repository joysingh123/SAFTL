<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\CompaniesWithDomain;
use App\Helpers\UtilString;
use App\CountryMaster;
use App\CompanyMaster;
use App\EmployeeSizeMaster;
use App\IndustryMaster;

class PopulateCompanyMaster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:companymaster';

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
        $companies = CompaniesWithDomain::where("status",'not processed')->take($limit)->get();
        foreach($companies AS $company){
            $id = $company->id;
            $linkedin_id = $company->linkedin_id;
            $company_name = trim($company->company_name);
            $website = trim($company->website);
            $linkedin_url = trim($company->linkedin_url);
            $employee_size = trim($company->employee_size);
            $industry = trim($company->industry);
            $city = trim($company->city);
            $country = trim($company->country);
            $postal_code = trim($company->postal_code);
            $domain = trim($company->company_domain);
            $state = trim($company->state);
            $region = trim($company->region);
            $grouping = trim($company->grouping);
            $logo_url = trim($company->logo_url);
            $facebook_url = trim($company->facebook_url);
            $twitter_url = trim($company->twitter_url);
            $zoominfo_url = trim($company->zoominfo_url);
            if(!UtilString::is_empty_string($employee_size) && !UtilString::is_empty_string($industry) && !UtilString::is_empty_string($country)){
                $country_id = 0;
                $industry_id = 0;
                $employee_size_id = 0;
                $country_data = CountryMaster::where('Country Name',$country)->where('Status','Active')->get();
                $industry_data = IndustryMaster::where('Industry',$industry)->where('Status','Active')->get();
                $employee_size_data = EmployeeSizeMaster::where('employee_size',$employee_size)->where('Status','Active')->get();
                if($country_data->count() > 0){
                    $country_id = $country_data->first()->ID;
                }
                if($industry_data->count() > 0){
                    $industry_id = $industry_data->first()->ID;
                }
                if($employee_size_data->count() > 0){
                    $employee_size_id = $employee_size_data->first()->ID;
                }
                if($country_id > 0 && $industry_id > 0 && $employee_size_id > 0){
                    $company_master = CompanyMaster::where("domain",$domain)->get();
                    if($company_master->count() > 0){
                        $company->status = 'processed';
                        $company->save();
                    }else{
                        $co = new CompanyMaster();
                        $co->id = $id;
                        $co->linkedin_id = $linkedin_id;
                        $co->company_name = (!UtilString::is_empty_string($company_name)) ? $company_name : NULL;
                        $co->website = (!UtilString::is_empty_string($website)) ? $website : NULL;
                        $co->linkedin_URL = (!UtilString::is_empty_string($linkedin_url)) ? $linkedin_url : NULL;
                        $co->employee_size = $employee_size_id;
                        $co->industry = $industry_id;
                        $co->country = $country_id;
                        $co->city = (!UtilString::is_empty_string($city)) ? $city : NULL;
                        $co->postal_code = (!UtilString::is_empty_string($postal_code)) ? $postal_code : NULL;
                        $co->domain = (!UtilString::is_empty_string($domain)) ? $domain : NULL;
                        $co->state = (!UtilString::is_empty_string($state)) ? $state : NULL;
                        $co->region = (!UtilString::is_empty_string($region)) ? $region : NULL;
                        $co->grouping = (!UtilString::is_empty_string($grouping)) ? $grouping : NULL;
                        $co->Logo_URL = (!UtilString::is_empty_string($logo_url)) ? $logo_url : NULL;
                        $co->facebook_url = (!UtilString::is_empty_string($facebook_url)) ? $facebook_url : NULL;
                        $co->twitter_url = (!UtilString::is_empty_string($twitter_url)) ? $twitter_url : NULL;
                        $co->zoominfo_url = (!UtilString::is_empty_string($zoominfo_url)) ? $zoominfo_url : NULL;
                        $save_as = $co->save();
                        if($save_as){
                            $company->status = 'processed';
                            $company->save(); 
                        }
                    }
                }else{
                    $company->status = 'attempt';
                    $company->save();
                }
            }else{
                $company->status = 'attempt';
                $company->save();
            }
        }
        UtilDebug::debug("End Processing");
    }
}
