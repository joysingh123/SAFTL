<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\CompanyMaster;
use App\Companies;
use App\ContactMaster;
use App\DepartmentMaster;
use App\TitleLevelMaster;
use App\BounceEmail;
use DB;
use App\SalesbotContacts;

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
        $department_master = DepartmentMaster::all();
        $title_level_master = TitleLevelMaster::all();
        $limit = 1000;
        $contact_master = DB::table('contact_master')->select(DB::raw("company_id,count(company_id) AS total_record"))->where('status',"not processed")->groupBy('company_id')->take($limit)->get();
        if($contact_master->count() > 0){
            foreach($contact_master AS $cm){
                $company_id = $cm->company_id;
                $company_master = CompanyMaster::where('id',$company_id)->get();
                if($company_master->count() > 0){
                    $company_name = trim($company_master->first()->company_name);
                    $website = trim($company_master->first()->website);
                    $linkedin_url = trim($company_master->first()->linkedin_URL);
                    $industry = trim($company_master->first()->industry);
                    $employee_size = trim($company_master->first()->employee_size);
                    $country = trim($company_master->first()->country);
                    $domain = trim($company_master->first()->domain);
                    $postal_code = trim($company_master->first()->postal_code);
                    $state = trim($company_master->first()->state);
                    $region = trim($company_master->first()->region);
                    $grouping = trim($company_master->first()->grouping);
                    $logo_url = trim($company_master->first()->Logo_URL);
                    $facebook_url = trim($company_master->first()->facebook_url);
                    $twitter_url = trim($company_master->first()->twitter_url);
                    $zoominfo_url = trim($company_master->first()->zoominfo_url);
                    $companies = Companies::where('domain',$domain)->get();
                    $reference_id = 0;
                    if($companies->count() > 0){
                        $reference_id = $companies->first()->id;
                        $company_master->first()->reference_id = $reference_id;
                        $company_master->first()->status = 'already exist';
                        $company_master->first()->save();
                    }else{
                        $salesbot_company = new Companies();
                        $salesbot_company->company = $company_name;
                        $salesbot_company->employeeSize = $employee_size;
                        $salesbot_company->companyWebsite = $website;
                        $salesbot_company->industry = $industry;
                        $salesbot_company->country_id = $country;
                        $salesbot_company->zipcode = $postal_code;
                        $salesbot_company->domain = $domain;
                        $salesbot_company->state = $state;
                        $salesbot_company->region = $region;
                        $salesbot_company->grouping = $grouping;
                        $salesbot_company->logo_url = $logo_url;
                        $salesbot_company->facebook_url = $facebook_url;
                        $salesbot_company->twitter_url = $twitter_url;
                        $salesbot_company->zoominfo_url = $zoominfo_url;
                        $save_as = $salesbot_company->save();
                        if($save_as){
                            $reference_id = $salesbot_company->id;
                            $company_master->first()->reference_id = $reference_id;
                            $company_master->first()->status = "processed";
                            $company_master->first()->save();
                        }
                    }
                    $contacts = ContactMaster::where('company_id',$company_id)->get();
                    if($contacts->count() > 0){
                        foreach($contacts AS $c){
                            $full_name = $c->full_name;
                            $first_name = $c->first_name;
                            $last_name = $c->last_name;
                            $email = $c->email;
                            $domain = $c->domain;
                            $location = $c->location;
                            $job_title = $c->job_title;
                            $title_level = $c->title_level;
                            $department = $c->department;
                            $s_department = NULL;
                            $s_title_level = NULL;
                            if($department > 0){
                                $dep_m = $department_master->where('ID',$department)->values();
                                $s_department = $dep_m->first()->Department;
                            }
                            if($title_level > 0){
                                $title_level_m = $title_level_master->where('ID',$title_level)->values();
                                $s_title_level = $title_level_m->first()->title_level;
                            }
                            $country_id = $c->country_id;
                            $email_status = $c->email_status;
                            $email_validation_date = $c->email_validation_date;
                            $salesbot_contact = SalesbotContacts::where('fname',$first_name)->where('lname',$last_name)->where('email',$email)->get();
                            if($salesbot_contact->count()){
                                $c->status = 'already exist';
                                $c->salesbot_ref_id = $salesbot_contact->first()->id;
                                $c->save();
                            }else{
                                $salesbot_contact_new = new SalesbotContacts();
                                $salesbot_contact_new->full_name = $full_name;
                                $salesbot_contact_new->fname = $first_name;
                                $salesbot_contact_new->lname = $last_name;
                                $salesbot_contact_new->email = $email;
                                $salesbot_contact_new->domain = $domain;
                                $salesbot_contact_new->location = $location;
                                $salesbot_contact_new->title_level = $title_level;
                                $salesbot_contact_new->s_department = $s_department;
                                $salesbot_contact_new->s_title_level = $s_title_level;
                                $salesbot_contact_new->company_id = $reference_id;
                                $salesbot_contact_new->country_id = $country_id;
                                $salesbot_contact_new->job_title = $job_title;
                                $salesbot_contact_new->department = $department;
                                $salesbot_contact_new->email_status = $email_status;
                                $salesbot_contact_new->email_validation_date = $email_validation_date;
                                $salesbot_contact_new->save();
                                $c->status = 'processed';
                                $c->salesbot_ref_id = $salesbot_contact_new->id;
                                $c->save();
                            }
                        }  
                    }
                }
            }
        }else{
            echo "No, record found for processing";
        }
        UtilDebug::debug("End Processing");
    }
}