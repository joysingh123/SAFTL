<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\UsCompaniesAll;
use App\CompaniesWithDomain;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use DB;
class PopulateCompaniesWithDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:companieswithdomain';

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
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $limit = 2000;
        $us_companies_data = UsCompaniesAll::where('status','not processed')->take($limit)->get();
        if($us_companies_data->count() > 0){
            $processed_data = 0;
            foreach($us_companies_data AS $us_data){
                $SourceURL =  $us_data->SourceURL;
                $LinkedInId =  $us_data->LinkedInId;
                $CompanyName =  $us_data->CompanyName;
                $website =  $us_data->website;
                $industry =  $us_data->industry;
                $locality =  $us_data->locality;
                $type =  $us_data->type;
                $companySize =  $us_data->companySize;
                $postalCode =  $us_data->postalCode;
                $countryName =  $us_data->countryName;
                if($LinkedInId > 0){
                    $company_with_domain = CompaniesWithDomain::where('linkedin_id',$LinkedInId)->get();
                    if($company_with_domain->count() == 0){
                        $website = UtilString::get_domain_from_url($website);
                        $company = new CompaniesWithDomain();
                        $company->user_id = 0;
                        $company->linkedin_id = $LinkedInId;
                        $company->linkedin_url = UtilString::is_empty_string($SourceURL) ? "" : $SourceURL;
                        $company->company_domain = $website;
                        $company->company_name = UtilString::is_empty_string($CompanyName) ? "" : $CompanyName;
                        $company->company_type = $type;
                        $company->employee_count_at_linkedin = 0;
                        $company->industry = $industry;
                        $company->city = $locality;
                        $company->postal_code = UtilString::is_empty_string($postalCode) ? 0 : $postalCode;
                        $company->employee_size = $companySize;
                        $company->country = $countryName;
                        $save = $company->save();
                        if($save){
                            $processed_data ++;
                            UsCompaniesAll::where('LinkedInId',$LinkedInId)->update(['status'=>'processed']);
                        }
                    }else{
                        $company_with_domain = $company_with_domain->first();
                        $company_with_domain->linkedin_url = (UtilString::is_empty_string($company_with_domain->linkedin_url) && !UtilString::is_empty_string($SourceURL)) ? $SourceURL : $company_with_domain->linkedin_url;
                        $company_with_domain->company_domain = (UtilString::is_empty_string($company_with_domain->company_domain) && !UtilString::is_empty_string($website)) ? $website : $company_with_domain->company_domain;
                        $company_with_domain->company_name = (UtilString::is_empty_string($company_with_domain->company_name) && !UtilString::is_empty_string($CompanyName)) ? $CompanyName : $company_with_domain->company_name;
                        $company_with_domain->company_type = (UtilString::is_empty_string($company_with_domain->company_type) && !UtilString::is_empty_string($type)) ? $type : $company_with_domain->company_type;
                        $company_with_domain->industry = (UtilString::is_empty_string($company_with_domain->industry) && !UtilString::is_empty_string($industry)) ? $industry : $company_with_domain->industry;
                        $company_with_domain->city = (UtilString::is_empty_string($company_with_domain->city) && !UtilString::is_empty_string($locality)) ? $locality : $company_with_domain->city;
                        $company_with_domain->postal_code = (UtilString::is_empty_string($company_with_domain->postal_code) && !UtilString::is_empty_string($postalCode)) ? $postalCode : $company_with_domain->postal_code;
                        $company_with_domain->employee_size = (UtilString::is_empty_string($company_with_domain->employee_size) && !UtilString::is_empty_string($companySize)) ? $companySize : $company_with_domain->employee_size;
                        $company_with_domain->country = (UtilString::is_empty_string($company_with_domain->country) && !UtilString::is_empty_string($countryName)) ? $countryName : $company_with_domain->country;
                        $save = $company_with_domain->save();
                        if($save){
                            $processed_data ++;
                            UsCompaniesAll::where('LinkedInId',$LinkedInId)->update(['status'=>'processed']);
                        }
                    }
                }
            }
            if($processed_data > 0){
                CompaniesWithDomain::where('employee_size', '0-1 employees')->update(['employee_size' => '1 to 10']);
                CompaniesWithDomain::where('employee_size', '1,001-5,000 employees')->update(['employee_size' => '1001 to 5000']);
                CompaniesWithDomain::where('employee_size', '1-10 employees')->update(['employee_size' => '1 to 10']);
                CompaniesWithDomain::where('employee_size', '10')->update(['employee_size' => '1 to 10']);
                CompaniesWithDomain::where('employee_size', '10,001+ employees')->update(['employee_size' => '10000 above']);
                CompaniesWithDomain::where('employee_size', '10001 + Employees')->update(['employee_size' => '10000 above']);
                CompaniesWithDomain::where('employee_size', '1001-5000 employees')->update(['employee_size' => '1001 to 5000']);
                CompaniesWithDomain::where('employee_size', '11-50 employees')->update(['employee_size' => '11 to 50']);
                CompaniesWithDomain::where('employee_size', '2-10 employees')->update(['employee_size' => '1 to 10']);
                CompaniesWithDomain::where('employee_size', '201-500 employees')->update(['employee_size' => '201 to 500']);
                CompaniesWithDomain::where('employee_size', '5,001-10,000 employees')->update(['employee_size' => '5001 to 10000']);
                CompaniesWithDomain::where('employee_size', '5001 - 10000 employees')->update(['employee_size' => '5001 to 10000']);
                CompaniesWithDomain::where('employee_size', '5001-10,000 employees')->update(['employee_size' => '5001 to 10000']);
                CompaniesWithDomain::where('employee_size', '5001-10000 employees')->update(['employee_size' => '5001 to 10000']);
                CompaniesWithDomain::where('employee_size', '501-1,000 employees')->update(['employee_size' => '501 to 1000']);
                CompaniesWithDomain::where('employee_size', '501-1000 employees')->update(['employee_size' => '501 to 1000']);
                CompaniesWithDomain::where('employee_size', '51-200 employees')->update(['employee_size' => '51 to 200']);
                CompaniesWithDomain::where('employee_size', 'Myself Only')->update(['employee_size' => '1 to 10']);
                CompaniesWithDomain::where('employee_size', 'NA')->update(['employee_size' => 'Invalid']);
                DB::statement("update contacts A inner join companies_with_domain B on A.linkedin_id = B.linkedin_id set A.process_for_contact_match = 'not processed' where A.process_for_contact_match = 'company not found'");                   
            }
        }
        UtilDebug::debug("end processing");
    }
}
