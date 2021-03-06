<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\Contacts;
use App\TitleLevelMaster;
use App\DepartmentMaster;
use App\ContactMaster;
use App\CompanyMaster;
use App\CountryMaster;
use App\Location;
class PopulateContactMaster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:contactmaster';

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
        $limit = 2000;
        $contacts = Contacts::where("populate_status",'not processed')->whereNotNull('email')->take($limit)->get();
        if($contacts->count() > 0){
            foreach($contacts AS $contact){
                $full_name = trim($contact->full_name);
                $first_name = trim($contact->first_name);
                $last_name = trim($contact->last_name);
                $email = trim($contact->email);
                $company_name = trim($contact->company_name);
                $job_title = trim($contact->job_title);
                $location = trim($contact->location);
                $title_level = trim($contact->title_level);
                $department = trim($contact->department);
                $email_status = trim($contact->email_status);
                $email_validation_date = $contact->email_validation_date;
                $domain = trim($contact->domain);
                $contact_country = trim($contact->contact_country);
                $title_level_id = 0;
                $department_level_id = 0;
                $country_id = 0;
                $city = NULL;
                $state = NULL;
                $country = NULL;
                $location_data = Location::where('location',$location)->get();
                if($location_data->count() > 0){
                    $city = $location_data->first()->city;
                    $state = $location_data->first()->state;
                    $country = $location_data->first()->country;
                    $contact_country = (UtilString::is_empty_string($contact_country)) ? $country : $contact_country;
                }
                $title_level_data = TitleLevelMaster::where('title_level',$title_level)->where('Status','Active');
                $department_level_data = DepartmentMaster::where('Department',$department)->where('Status','Active');
                $country_data = CountryMaster::where('Country Name',$contact_country)->where('Status','Active');
                if($title_level_data->count() > 0){
                    $title_level_id = $title_level_data->first()->ID;
                }
                if($department_level_data->count() > 0){
                    $department_level_id = $department_level_data->first()->ID;
                }
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
                $contact_exist = ContactMaster::where('first_name',$first_name)->where('last_name',$last_name)->where('domain',$domain)->get();
                if($contact_exist->count() > 0){
                    $company_data = CompanyMaster::where("domain",$domain)->get();
                    $contact_exist->first()->full_name = (!UtilString::is_empty_string($full_name)) ? $full_name : NULL;
                    $contact_exist->first()->first_name = $first_name;
                    $contact_exist->first()->last_name = $last_name;
                    $contact_exist->first()->email = $email;
                    $contact_exist->first()->company_name = $company_name;
                    $contact_exist->first()->job_title = $job_title;
                    $contact_exist->first()->location = $location;
                    $contact_exist->first()->city = $city;
                    $contact_exist->first()->state = $state;
                    $contact_exist->first()->country = $country;
                    $contact_exist->first()->title_level = $title_level_id;
                    $contact_exist->first()->s_title_level = $title_level;
                    $contact_exist->first()->department = $department_level_id;
                    $contact_exist->first()->s_department = $department;
                    $contact_exist->first()->email_status = $email_status;
                    $contact_exist->first()->email_validation_date = $email_validation_date;
                    $contact_exist->first()->domain = $domain;
                    $contact_exist->first()->company_id = 0;
                    $contact_exist->first()->country_id = $country_id;
                    if($company_data->count() > 0){
                        $contact_exist->first()->company_id = $company_data->first()->id;
                    }
                    $contact_exist->first()->continent = $continent;
                    $contact_exist->first()->continent_region = $continent_region;
                    $contact_exist->first()->mena = $mena;
                    $contact_exist->first()->apac = $apac;
                    $contact_exist->first()->latam = $latam;
                    $contact_exist->first()->europian_union = $europian_union;
                    $contact_exist->first()->emea = $emea;
                    $contact_exist->first()->save();
                    $contact->populate_status = 'processed';
                    $contact->save();
                }else{
                    $company_data = CompanyMaster::where("domain",$domain)->get();
                    $con = new ContactMaster();
                    $con->full_name = (!UtilString::is_empty_string($full_name)) ? $full_name : NULL;
                    $con->first_name = $first_name;
                    $con->last_name = $last_name;
                    $con->email = $email;
                    $con->company_name = $company_name;
                    $con->job_title = $job_title;
                    $con->location = $location;
                    $con->city = $city;
                    $con->state = $state;
                    $con->country = $country;
                    $con->title_level = $title_level_id;
                    $con->s_title_level = $title_level;
                    $con->department = $department_level_id;
                    $con->s_department = $department;
                    $con->email_status = $email_status;
                    $con->email_validation_date = $email_validation_date;
                    $con->domain = $domain;
                    $con->company_id = 0;
                    $con->country_id = $country_id;
                    if($company_data->count() > 0){
                        $con->company_id = $company_data->first()->id;
                    }
                    $con->continent = $continent;
                    $con->continent_region = $continent_region;
                    $con->mena = $mena;
                    $con->apac = $apac;
                    $con->latam = $latam;
                    $con->europian_union = $europian_union;
                    $con->emea = $emea;
                    $save_as = $con->save();
                    if($save_as){
                        $contact->populate_status = 'processed';
                        $contact->save();
                    }
                }
            }
        }else{
            echo "No record found for processing";
        }
        UtilDebug::debug("End Processing");
    }
}
