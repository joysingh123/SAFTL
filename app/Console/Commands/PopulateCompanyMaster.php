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
            $company_name = trim($company->company_name);
            $website = trim($company->website);
            $linkedin_url = trim($company->linkedin_url);
            $employee_size = trim($company->employee_size);
            $industry = trim($company->industry);
            $city = trim($company->city);
            $country = trim($company->country);
            $postal_code = trim($company->postal_code);
            $domain = trim($company->company_domain);
            if(!UtilString::is_empty_string($employee_size) && !UtilString::is_empty_string($industry) && !UtilString::is_empty_string($country)){
                $country_id = 0;
                $industry_id = 0;
                $employee_size_id = 0;
                $country_data = CountryMaster::where('Country Name',$country)->get();
                $industry_data = IndustryMaster::where('Industry',$industry)->get();
                $employee_size_data = EmployeeSizeMaster::where('employee_size',$employee_size)->get();
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
                        $co->company_name = (!UtilString::is_empty_string($company_name)) ? $company_name : NULL;
                        $co->website = (!UtilString::is_empty_string($website)) ? $website : NULL;
                        $co->linkedin_URL = (!UtilString::is_empty_string($linkedin_url)) ? $linkedin_url : NULL;
                        $co->employee_size = $employee_size_id;
                        $co->industry = $industry_id;
                        $co->country = $country_id;
                        $co->city = (!UtilString::is_empty_string($city)) ? $city : NULL;
                        $co->postal_code = (!UtilString::is_empty_string($postal_code)) ? $postal_code : NULL;
                        $co->domain = (!UtilString::is_empty_string($domain)) ? $domain : NULL;
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
