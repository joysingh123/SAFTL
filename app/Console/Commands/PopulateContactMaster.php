<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\Contacts;
use App\TitleLevelMaster;
use App\DepartmentMaster;
use App\ContactMaster;
use App\CompaniesWithDomain;
use App\CountryMaster;

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
        $limit = 1000;
        $contacts = Contacts::where("populate_status",'not processed')->take($limit)->get();
        if($contacts->count() > 0){
            foreach($contacts AS $contact){
                $id = $contact->id;
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
                if($email_status == 'valid' && !UtilString::is_empty_string($job_title) && !UtilString::is_empty_string($title_level)){
                    $title_level_id = 0;
                    $department_level_id = 0;
                    $country_id = 0;
                    $title_level_data = TitleLevelMaster::where('title_level',$title_level)->where('Status','Active');
                    $department_level_data = DepartmentMaster::where('Department',$department)->where('Status','Active');
                    $country_data = CountryMaster::where('Country Name',$contact_country)->where('Status','Active');
                    if($title_level_data->count() > 0){
                        $title_level_id = $title_level_data->first()->ID;
                    }
                    if($department_level_data->count() > 0){
                        $department_level_id = $department_level_data->first()->ID;
                    }
                    if($country_data->count() > 0){
                        $country_id = $country_data->first()->ID;
                    }
                    if($title_level_id > 0){
                        $contact_exist = ContactMaster::where('first_name',$first_name)->where('last_name',$last_name)->where('domain',$domain)->get();
                        if($contact_exist->count() > 0){
                            $contact->populate_status = 'processed';
                            $contact->save();
                        }else{
                            $company_data = CompaniesWithDomain::where("company_domain",$domain)->get();
                            $con = new ContactMaster();
                            $con->id = $id;
                            $con->first_name = $first_name;
                            $con->last_name = $last_name;
                            $con->email = $email;
                            $con->company_name = $company_name;
                            $con->job_title = $job_title;
                            $con->location = $location;
                            $con->title_level = $title_level_id;
                            $con->department = $department_level_id;
                            $con->email_status = $email_status;
                            $con->email_validation_date = $email_validation_date;
                            $con->domain = $domain;
                            $con->company_id = 0;
                            $con->country_id = $country_id;
                            if($company_data->count() > 0){
                                $con->company_id = $company_data->first()->id;
                            }
                            $save_as = $con->save();
                            if($save_as){
                                $contact->populate_status = 'processed';
                                $contact->save();
                            }
                        }
                    }else{
                        $contact->populate_status = 'title or department not found';
                        $contact->save();
                    }
                }else{
                    $contact->populate_status = 'attempt';
                    $contact->save();
                }
            }
        }else{
            echo "No record found for processing";
        }
        UtilDebug::debug("End Processing");
    }
}
