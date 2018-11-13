<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\CompanyMaster;
use App\Companies;
use App\ContactMaster;
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
                            $first_name = $c->first_name;
                            $last_name = $c->last_name;
                            $email = $c->email;
                            $title_level = $c->title_level;
                            $department = $c->department;
                            $salesbot_contact = SalesbotContacts::where('fname',$first_name)->where('lname',$last_name)->where('email',$email)->get();
                            if($salesbot_contact->count()){
                                $c->status = 'already exist';
                                $c->salesbot_ref_id = $salesbot_contact->first()->id;
                                $c->save();
                            }else{
                                $salesbot_contact_new = new SalesbotContacts();
                                $salesbot_contact_new->fname = $first_name;
                                $salesbot_contact_new->lname = $last_name;
                                $salesbot_contact_new->email = $email;
                                $salesbot_contact_new->company = $reference_id;
                                $salesbot_contact_new->job_title = $title_level;
                                $salesbot_contact_new->department = $department;
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
