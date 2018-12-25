<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MatchedContact;
use App\AvailableEmail;
use App\Helpers\UtilDebug;
use DB;

class GenearteDefaultFormat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:defaultformat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Default Email Format';

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
        $limit = 2000;
        $emp_size_1 = ['501 to 1000','5001 to 10000','201 to 500','1001 to 5000','10000 above'];
        $emp_size_2 = ['invalid','NA','1 to 10','11 to 50','51 to 200'];
        $matched_contacts_formate_1 = DB::table('matched_contacts')
                    ->select(DB::raw("distinct 0 AS user_id, CONCAT('FIRSTNAME.LASTNAME@',domain) AS email,domain AS company_domain,'FIRSTNAME' AS first_name,'LASTNAME' as last_name,'' as country,' ' as job_title,'' as status,employee_size"))
                    ->where('email_format_available', '=', 'no')
                    ->whereNull('process_status')
                    ->take($limit)
                    ->get();
        echo "processing record: $matched_contacts_formate_1";
        if($matched_contacts_formate_1->count() > 0){
            $ids_for_match = $matched_contacts_formate_1->pluck('id');
            $ids_for_match = $ids_for_match->all();
            MatchedContact::whereIn('id',$ids_for_match)->update(['process_status'=>'processed']);
            foreach ($matched_contacts_formate_1 AS $mf){
                $email = $mf->email;
                $company_domain = $mf->company_domain;
                $employee_size = $mf->employee_size;
                $exist_in_available_email = AvailableEmail::where('email',$email)->count();
                if($exist_in_available_email <= 0){
                    $available_email = new AvailableEmail();
                    $available_email->user_id = $mf->user_id;
                    $available_email->email = $mf->email;
                    $available_email->company_domain = $mf->company_domain;
                    $available_email->first_name = $mf->first_name;
                    $available_email->last_name = $mf->last_name;
                    $available_email->country = $mf->country;
                    $available_email->job_title = $mf->job_title;
                    $available_email->status = $mf->status;
                    $available_email->save();
                }
                if(in_array($employee_size, $emp_size_1)){
                    $email = "FLASTNAME@$company_domain";
                    $exist_in_available_email = AvailableEmail::where('email',$email)->count();
                    if($exist_in_available_email <= 0){
                        $available_email = new AvailableEmail();
                        $available_email->user_id = $mf->user_id;
                        $available_email->email = $email;
                        $available_email->company_domain = $mf->company_domain;
                        $available_email->first_name = $mf->first_name;
                        $available_email->last_name = $mf->last_name;
                        $available_email->country = $mf->country;
                        $available_email->job_title = $mf->job_title;
                        $available_email->status = $mf->status;
                        $available_email->save();
                    }
                }
                
                if(in_array($employee_size, $emp_size_2)){
                    $email = "FIRSTNAME@$company_domain";
                    $exist_in_available_email = AvailableEmail::where('email',$email)->count();
                    if($exist_in_available_email <= 0){
                        $available_email = new AvailableEmail();
                        $available_email->user_id = $mf->user_id;
                        $available_email->email = $email;
                        $available_email->company_domain = $mf->company_domain;
                        $available_email->first_name = $mf->first_name;
                        $available_email->last_name = $mf->last_name;
                        $available_email->country = $mf->country;
                        $available_email->job_title = $mf->job_title;
                        $available_email->status = $mf->status;
                        $available_email->save();
                    }
                }
            }
//            $result = json_decode($matched_contacts_formate_1, true);
//            $insertData = DB::table('available_email')->insert($result);
        }
//        
//        $matched_contacts_formate_2 = DB::table('matched_contacts')
//                    ->select(DB::raw("distinct 0 AS user_id, CONCAT('FLASTNAME@',domain) AS email,domain AS company_domain,'FIRSTNAME' AS first_name,'LASTNAME' as last_name,'' as country,' ' as job_title,'' as status"))
//                    ->where("email_format_available", '=', 'no')
//                    ->whereIn('employee_size',['501 to 1000','5001 to 10000','201 to 500','1001 to 5000','10000 above'])
//                    ->take($limit)
//                    ->get();
//        
//        if($matched_contacts_formate_2->count() > 0){
//            $result = json_decode($matched_contacts_formate_2, true);
//            $insertData = DB::table('available_email')->insert($result);
//        }
//        
//        $matched_contacts_formate_3 = DB::table('matched_contacts')
//                    ->select(DB::raw("distinct 0 AS user_id, CONCAT('FIRSTNAME@',domain) AS email,domain AS company_domain,'FIRSTNAME' AS first_name,'LASTNAME' as last_name,'' as country,' ' as job_title,'' as status"))
//                    ->where('email_format_available', '=', 'no')
//                    ->whereIn('employee_size',['invalid','NA','1 to 10','11 to 50','51 to 200'])
//                    ->take($limit)
//                    ->get();
//        if($matched_contacts_formate_3->count() > 0){
//            $result = json_decode($matched_contacts_formate_3, true);
//            $insertData = DB::table('available_email')->insert($result);
//        }
        UtilDebug::debug("end processing");
    }
}
