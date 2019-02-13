<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\MatchedContact;
use App\EmailFormatForInvalid;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\Helpers\UtilConstant;
use App\Emails;
class CreateEmailForInvalidContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:emailforinvalid';

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
        $email_format = EmailFormatForInvalid::where('status','Active')->get();
        $matched_contacts = MatchedContact::where('email_status','invalid')->where('process_status','reprocess')->take($limit)->get();
        if($matched_contacts->count() > 0){
            foreach ($matched_contacts AS $mc){
                $first_name = strtolower(trim($mc->first_name));
                $last_name = strtolower(trim($mc->last_name));
                $domain = strtolower(trim($mc->domain));
                $matched_contact_id = $mc->id;
                $domain = UtilString::get_domain_from_url($domain);
                $is_process = TRUE;
                $msg = NULL;
                if (UtilString::is_empty_string($domain)) {
                    $is_process = FALSE;
                    $msg = "domain not found";
                }
                if (!UtilString::contains($domain,".")) {
                    $is_process = FALSE;
                    $msg = "invalid domain";
                }
                if (UtilString::contains($domain,"@")) {
                    $d_array = explode("@", $domain);
                    if(isset($d_array[1]) && !UtilString::is_empty_string($d_array[1]) && UtilString::contains($d_array[1], ".")){
                        $domain = $d_array[1];
                    }
                }
                if (UtilString::is_empty_string($first_name) || UtilString::is_empty_string($last_name)) {
                    $is_process = FALSE;
                    $msg = "first_name or last_name not found";
                }
                if(UtilString::contains($first_name, "(") || UtilString::contains($first_name, ")") || UtilString::contains($last_name, "(") || UtilString::contains($last_name, ")")){
                    $is_process = FALSE;
                    $msg = "first_name or last_name contains ( or )";
                }
                if(UtilString::contains($first_name, "#") || UtilString::contains($last_name, "#")){
                    $is_process = FALSE;
                    $msg = "first_name or last_name contains #";
                }
                if(strlen($first_name) == 1 || strlen($last_name == 1)){
                    $is_process = FALSE;
                    $msg = "first_name or last_name having 1 char";
                }
                if(($first_name == 'N/A' || $first_name == 'n/a') || ($last_name == 'N/A' || $last_name == 'n/a')){
                    $is_process = FALSE;
                    $msg = "first_name or last_name contains N/A";
                }
                if($is_process){
                    $first_name_first_char = substr($first_name, 0, 1);
                    $last_name_first_char = substr($last_name, 0, 1);
                    $first_name_first_two_char = substr($first_name, 0, 2);
                    $last_name_first_two_char = substr($last_name, 0, 2);
                    $vars = array(
                                UtilConstant::FIRST_NAME => $first_name,
                                UtilConstant::LAST_NAME => $last_name,
                                UtilConstant::FIRST_NAME_FIRST_CHARACTER => $first_name_first_char,
                                UtilConstant::LAST_NAME_FIRST_CHARACTER => $last_name_first_char,
                                UtilConstant::FIRST_NAME_FIRST_TWO_CHARACTER => $first_name_first_two_char,
                                UtilConstant::LAST_NAME_FIRST_TWO_CHARACTER => $last_name_first_two_char,
                                UtilConstant::DOMAIN => $domain
                            );
                    if($email_format->count() > 0){
                        foreach ($email_format AS $ef){
                            $e_formate = $ef->email_format;
                            $e_formate_doublequtes = "$e_formate";
                            $email = str_replace("'", "", strtr($e_formate_doublequtes, $vars));
                            $emails = new Emails();
                            $emails->matched_contact_id = $matched_contact_id;
                            $emails->email = $email;
                            $emails->status = "success";
                            $emails->save();
                        }
                        $mc->process_status = 'processed';
                        $mc->save();
                    }
                }else{
                    $mc->process_status = $msg;
                    $mc->save();
                }
            }
        }else{
            echo "No, Data Found For Processing";
        }
        UtilDebug::debug("End Processing");
    }
}
