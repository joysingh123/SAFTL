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
        $matched_contacts_formate_1 = DB::table('matched_contacts')
                    ->select(DB::raw("distinct 0 AS user_id, CONCAT('FIRSTNAME.LASTNAME@',domain) AS email,' ' AS company_domain,domain AS company_domain,'FIRSTNAME' AS first_name,'LASTNAME' as last_name,'' as country,' ' as job_title,'' as status"))
                     ->where('email_format_available', '=', 'no')
                     ->get();
        
        if($matched_contacts_formate_1->count() > 0){
            $result = json_decode($matched_contacts_formate_1, true);
            $insertData = DB::table('available_email')->insert($result);
        }
        
        $matched_contacts_formate_2 = DB::table('matched_contacts')
                    ->select(DB::raw("distinct 0 AS user_id, CONCAT('FLASTNAME@',domain) AS email,' ' AS company_domain,domain AS company_domain,'FIRSTNAME' AS first_name,'LASTNAME' as last_name,'' as country,' ' as job_title,'' as status"))
                    ->where("email_format_available", '=', 'no')
                    ->whereIn('employee_size',['501 to 1000','5001 to 10000','201 to 500','1001 to 5000','10000 above'])
                    ->get();
        
        if($matched_contacts_formate_2->count() > 0){
            $result = json_decode($matched_contacts_formate_2, true);
            $insertData = DB::table('available_email')->insert($result);
        }
        
        $matched_contacts_formate_3 = DB::table('matched_contacts')
                    ->select(DB::raw("distinct 0 AS user_id, CONCAT('FIRSTNAME@',domain) AS email,' ' AS company_domain,domain AS company_domain,'FIRSTNAME' AS first_name,'LASTNAME' as last_name,'' as country,' ' as job_title,'' as status"))
                    ->where('email_format_available', '=', 'no')
                    ->whereIn('employee_size',['invalid','NA','1 to 10','11 to 50','51 to 200'])
                    ->get();
        if($matched_contacts_formate_3->count() > 0){
            $result = json_decode($matched_contacts_formate_3, true);
            $insertData = DB::table('available_email')->insert($result);
        }
        UtilDebug::debug("end processing");
    }
}
