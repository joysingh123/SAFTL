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
        $available_email = Emails::all(['email']);
        $email_array = $available_email->pluck('email');
        $plucked_all = $email_array->all();
        $emails = AvailableEmail::whereIn('email',$plucked_all)->take(100)->get();
        foreach($emails AS $email){
            $email_db = Emails::where('email',$email->email)->get();
            $matched_contact_id = $email_db->first()->matched_contact_id;
            echo $matched_contact_id.",";
            $matched_contact = MatchedContact::where('id',$matched_contact_id)->get();
            $mt = $matched_contact->first();
            $mt->email = $email_db->first()->email;
            $mt->email_status = 'valid';
            $mt->email_validation_date = '2018-09-01 00:00:00';
            $saved = $mt->save();
            if($saved){
               Emails::where('matched_contact_id',$matched_contact_id)->delete(); 
            }
        }
        UtilDebug::debug("End processing");
    }
}
