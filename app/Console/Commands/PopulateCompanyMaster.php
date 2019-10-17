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
        $limit = 10000;
        $companies = CompaniesWithDomain::where("status",'not processed')->take($limit)->get();
        foreach($companies AS $company){
            $company_name = trim($company->company_name);
            $website = trim($company->website);
            $linkedin_url = trim($company->linkedin_url);
            $linkedin_id = trim($company->linkedin_id);
            $employee_size = trim($company->employee_size);
            $employee_count = trim($company->employee_count_at_linkedin);
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
            $mx_record = $company->mx_record;
            $company_type = 'Private';
            if(UtilString::contains(strtolower($domain), '.gov')){
                $company_type = "Government";
            }else if(strtolower ($industry) == "nonprofit organization management"){
                $company_type = "Non-Profit";
            }
            $country_id = 0;
            $industry_id = 0;
            $employee_size_id = 0;
            $country_data = CountryMaster::where('Country Name',$country)->where('Status','Active')->get();
            $industry_data = IndustryMaster::where('Industry',$industry)->where('Status','Active')->get();
            $employee_size_data = EmployeeSizeMaster::where('employee_size',$employee_size)->where('Status','Active')->get();
            $continent = NULL;
            $continent_region = NULL;
            $mena = NULL;
            $apac = NULL;
            $latam = NULL;
            $europian_union = NULL;
            $emea = NULL;
            if($country_data->count() > 0){
                $country_id = $country_data->first()->ID;
                $continent = $country_data->first()->continent;
                $continent_region = $country_data->first()->continent_region;
                $mena = $country_data->first()->mena;
                $apac = $country_data->first()->apac;
                $latam = $country_data->first()->latam;
                $europian_union = $country_data->first()->eu;
                $emea = $country_data->first()->emea;
            }
            if($industry_data->count() > 0){
                $industry_id = $industry_data->first()->ID;
            }
            if($employee_size_data->count() > 0){
                $employee_size_id = $employee_size_data->first()->ID;
            }
            $company_master = CompanyMaster::where("domain",$domain)->get();
            if($company_master->count() > 0){
                $company_master->first()->linkedin_id = $linkedin_id;
                $company_master->first()->company_name = (!UtilString::is_empty_string($company_name)) ? $company_name : NULL;
                $company_master->first()->company_type = (!UtilString::is_empty_string($company_type)) ? $company_type : NULL;
                $company_master->first()->website = (!UtilString::is_empty_string($website)) ? $website : NULL;
                $company_master->first()->linkedin_URL = (!UtilString::is_empty_string($linkedin_url)) ? $linkedin_url : NULL;
                $company_master->first()->employee_count = $employee_count;
                $company_master->first()->employee_size = $employee_size_id;
                $company_master->first()->s_employee_size = $employee_size;
                $company_master->first()->industry = $industry_id;
                $company_master->first()->country = $country_id;
                $company_master->first()->s_industry = (!UtilString::is_empty_string($industry)) ? $industry : NULL;
                $company_master->first()->s_country = (!UtilString::is_empty_string($country)) ? $country : NULL;
                $company_master->first()->city = (!UtilString::is_empty_string($city)) ? $city : NULL;
                $company_master->first()->postal_code = (!UtilString::is_empty_string($postal_code)) ? $postal_code : NULL;
                $company_master->first()->domain = (!UtilString::is_empty_string($domain)) ? $domain : NULL;
                $company_master->first()->state = (!UtilString::is_empty_string($state)) ? $state : NULL;
                $company_master->first()->region = (!UtilString::is_empty_string($region)) ? $region : NULL;
                $company_master->first()->grouping = (!UtilString::is_empty_string($grouping)) ? $grouping : NULL;
                $company_master->first()->Logo_URL = (!UtilString::is_empty_string($logo_url)) ? $logo_url : NULL;
                $company_master->first()->continent = $continent;
                $company_master->first()->continent_region = $continent_region;
                $company_master->first()->mx_record = $mx_record;
                $company_master->first()->mena = $mena;
                $company_master->first()->apac = $apac;
                $company_master->first()->latam = $latam;
                $company_master->first()->europian_union = $europian_union;
                $company_master->first()->emea = $emea;
                $company_master->first()->facebook_url = (!UtilString::is_empty_string($facebook_url)) ? $facebook_url : NULL;
                $company_master->first()->twitter_url = (!UtilString::is_empty_string($twitter_url)) ? $twitter_url : NULL;
                $company_master->first()->zoominfo_url = (!UtilString::is_empty_string($zoominfo_url)) ? $zoominfo_url : NULL;
                $save_as = $company_master->first()->save();
                $company->status = 'processed';
                $company->save();
            }else{
                $co = new CompanyMaster();
                $co->linkedin_id = $linkedin_id;
                $co->company_name = (!UtilString::is_empty_string($company_name)) ? $company_name : NULL;
                $co->company_type = (!UtilString::is_empty_string($company_type)) ? $company_type : NULL;
                $co->website = (!UtilString::is_empty_string($website)) ? $website : NULL;
                $co->linkedin_URL = (!UtilString::is_empty_string($linkedin_url)) ? $linkedin_url : NULL;
                $co->employee_count = $employee_count;
                $co->employee_size = $employee_size_id;
                $co->s_employee_size = $employee_size;
                $co->industry = $industry_id;
                $co->country = $country_id;
                $co->s_industry = (!UtilString::is_empty_string($industry)) ? $industry : NULL;
                $co->s_country = (!UtilString::is_empty_string($country)) ? $country : NULL;
                $co->city = (!UtilString::is_empty_string($city)) ? $city : NULL;
                $co->postal_code = (!UtilString::is_empty_string($postal_code)) ? $postal_code : NULL;
                $co->domain = (!UtilString::is_empty_string($domain)) ? $domain : NULL;
                $co->state = (!UtilString::is_empty_string($state)) ? $state : NULL;
                $co->region = (!UtilString::is_empty_string($region)) ? $region : NULL;
                $co->grouping = (!UtilString::is_empty_string($grouping)) ? $grouping : NULL;
                $co->Logo_URL = (!UtilString::is_empty_string($logo_url)) ? $logo_url : NULL;
                $co->continent = $continent;
                $co->continent_region = $continent_region;
                $co->mx_record = $mx_record;
                $co->mena = $mena;
                $co->apac = $apac;
                $co->latam = $latam;
                $co->europian_union = $europian_union;
                $co->emea = $emea;
                $co->facebook_url = (!UtilString::is_empty_string($facebook_url)) ? $facebook_url : NULL;
                $co->twitter_url = (!UtilString::is_empty_string($twitter_url)) ? $twitter_url : NULL;
                $co->zoominfo_url = (!UtilString::is_empty_string($zoominfo_url)) ? $zoominfo_url : NULL;
                $save_as = $co->save();
                if($save_as){
                    $company->status = 'processed';
                    $company->save(); 
                }
            }   
        }
        UtilDebug::debug("End Processing");
    }
}
