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
        $result = array('Abhay.Patil@koltepatil.com','Abhineet.Jain@omkar.com','Anand@puravankara.com','Arshad.Khan@omkar.com','Arun.Anand@shriramproperties.com','Arun.Bodupali@lavasa.com','Arvind.Subramanian@lodhagroup.com','Ashish.Dhami@kalpataru.com','Ashish.Joshi@omkar.com','Ashwini.Kumar@niteshestates.com','Gaurav.Vishwakarma@propshop.org.in','Girish.Kamble@lodhagroup.com','Gopal.Sarda@koltepatil.com','Gurmeet@atsgreens.com','Harish@ansalapi.com','Himanshu@ansalapi.com','Hiralal.Khobragade@sunteckindia.com ','Janhavi.Sukhtankar@lodhagroup.com','JavedShaikh@rustomjee.com','jeremie.howlett@sbimf.com','Kuntal.Shah@omkar.com','MukeshBharti@smcrealty.com','Mukund.Rathi@lavasa.com','Nishanth.Vishwanath@mantri.in','Praveen.Sood@lavasa.com','Ps@royalenfield.com','Quentin.Devotta@360realtors.com','Rahul.Maroo@omkar.com','Rakesh.Gupta@lodhagroup.com','Ranjana.Singh@emaar-india.com','Ranjana@emaar-india.com','Ravindra@prestigeconstructions.com','Rupali.Nimbalkar@sunteckindia.com','Sandip.Shah@lodhagroup.com','Satish.Shenoy@lodhagroup.com','Selvaraj.Ramasamy@lodhagroup.com','Shatrughan.Singh@lodhagroup.com','SreePremnath@rustomjee.com','Subrahmanya@casagrand.co.in','Sujit.Jadhav@lodhagroup.com','Tara.Giridhar@mantri.in','Tarique.Ahmad@propshop.org.in','TT@abbott.com,Varun@atsgreens.com','Vilas.Nidsoshi@lodhagroup.com');
        if(count($result) > 0){
            foreach($result AS $email){
                $email_db = Emails::where('email',$email)->get();
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
        }
        UtilDebug::debug("End processing");
    }
}
