<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Emails;
use App\AvailableEmail;
use DB;
use App\Helpers\UtilDebug;
use App\MatchedContact;
class RemoveEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:email';

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
    public function handle(){
        UtilDebug::debug("start processing");
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $emails = Emails::all(['email']);
        $emails_array = $emails->pluck('email');
        $emails_all = $emails_array->all();
        $available_email = AvailableEmail::all(['email']);
        $available_array = $available_email->pluck('email');
        $available_all = $available_array->all();
        $result = array_intersect($emails_all,$available_all);
        if(count($result) > 0){
            foreach($result AS $email){
                $email_db = Emails::where('email',$email)->get();
                if($email_db->count() > 0){
                    $matched_contact_id = $email_db->first()->matched_contact_id;
                    echo $matched_contact_id.",";
                    if($matched_contact_id > 0){
                        $matched_contact = MatchedContact::where('id',$matched_contact_id)->get();
                        if($matched_contact->count() > 0){
                            $mt = $matched_contact->first();
                            $mt->email = $email_db->first()->email;
                            $mt->email_status = 'valid';
                            $mt->email_validation_date = '2018-09-01 00:00:00';
                            $saved = $mt->save();
                            if($saved){
                               Emails::where('matched_contact_id',$matched_contact_id)->delete(); 
                            }
                        }
                    }
                }
            }
        }
        UtilDebug::debug("End processing");
    }
}
