<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Emails;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use DB;
use App\Traits\ValidateEmailTraits;
use App\MatchedContact;
use App\Contacts;

class ValidateEmailCron2 extends Command {
    use ValidateEmailTraits;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:emailcron2';

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
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        UtilDebug::debug("start processing");
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $response = array();
        $limit = 250;
        $emails = DB::table('emails')
                        ->select('matched_contact_id', DB::raw("group_concat(email) AS emails"))
                        ->groupBy('matched_contact_id')
                        ->where('status', 'success')
                        ->take($limit)->get();
        if ($emails->count() > 0) {
            $plucked_email = $emails->pluck('matched_contact_id');
            $plucked_email_array = $plucked_email->all();
            $result = Emails::whereIn('matched_contact_id', $plucked_email_array)->update(['status' => 'cron2']);
            if ($result > 0) {
                foreach ($emails AS $email_record) {
                    $matched_id = $email_record->matched_contact_id;
                    Emails::where('matched_contact_id', '=', $matched_id)->update(['status' => 'Invalidate']);
                    $emails_db = $email_record->emails;
                    $emails_array = array();
                    if (UtilString::contains($emails_db, ",")) {
                        $emails_array = explode(",", $emails_db);
                    } else {
                        $emails_array[] = $emails_db;
                    }
                    $is_invalid = false;
                    foreach ($emails_array AS $email) {
                        $v_response = $this->validateEmail($email);
                        if ($v_response['email_status'] == 'valid' || $v_response['email_status'] == 'catch all') {
                            $is_invalid = false;
                            $email_status = $v_response['email_status'];
                            $email_validation_date = date("Y-m-d H:i:s");
                            $matched_contact = MatchedContact::where('id', '=', $matched_id)->first();
                            $matched_contact->email = $email;
                            $matched_contact->email_status = $v_response['email_status'];
                            $matched_contact->email_validation_date = date("Y-m-d H:i:s");
                            $matched_contact->save();
                            Emails::where('matched_contact_id', '=', $matched_id)->where('email', '=', $email)->update(['status' => $email_status]);
                            //Emails::where('matched_contact_id', '=', $matched_id)->update(['status' => $v_response['email_status']]);
                            $contact_id = $matched_contact->contact_id;
                            $domain = $matched_contact->domain;
                            Contacts::where('id','=',$contact_id)->update(['email'=>$email,'email_status'=>$email_status,'email_validation_date'=>$email_validation_date,'domain'=>$domain]);
                            break;
                        } else {
                            if ($v_response['email_status'] != "") {
                                Emails::where('matched_contact_id', '=', $matched_id)->where('email', '=', $email)->update(['status' => $v_response['email_status']]);
                                $is_invalid = true;
                            }else{
                                Emails::where('matched_contact_id', '=', $matched_id)->where('email', '=', $email)->update(['status' => 'timeout']);
                                $email_validation_date = date("Y-m-d H:i:s");
                                MatchedContact::where('id', '=', $matched_id)->update(['email_status' => 'timeout', 'email_validation_date' => $email_validation_date]);
                                $matched_contact = MatchedContact::where('id', '=', $matched_id)->first();
                                $contact_id = $matched_contact->contact_id;
                                $domain = $matched_contact->domain;
                                Contacts::where('id','=',$contact_id)->update(['email_status'=>'timeout','email_validation_date'=>$email_validation_date,'domain'=>$domain]);
                            }
                        }
                    }
                    if ($is_invalid) {
                        //Emails::where('matched_contact_id', '=', $matched_id)->update(['status' => 'invalid']);
                        $email_status = $v_response['email_status'];
                        $email_validation_date = date("Y-m-d H:i:s");
                        MatchedContact::where('id', '=', $matched_id)->update(['email_status' => $email_status, 'email_validation_date' => $email_validation_date]);
                        $matched_contact = MatchedContact::where('id', '=', $matched_id)->first();
                        $contact_id = $matched_contact->contact_id;
                        $domain = $matched_contact->domain;
                        Contacts::where('id','=',$contact_id)->update(['email_status'=>$email_status,'email_validation_date'=>$email_validation_date,'domain'=>$domain]);
                    }
                }
            }
        } else {
            $response['status'] = "fail";
            $response['status'] = "No, email found for validation";
        }
        print_r($response);
        UtilDebug::debug("end processing");
    }
}
