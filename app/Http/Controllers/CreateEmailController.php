<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\Helpers\UtilConstant;
use App\EmailFormat;
use App\MatchedContact;
use App\Emails;
class CreateEmailController extends Controller
{
    public function index(Request $request){
        $matched_contact = MatchedContact::where('email_status',null)->where('email_format_available','yes')->get();
        foreach($matched_contact AS $mt){
            UtilDebug::print_message("mt", $mt);
            $available_format_for_domain = EmailFormat::where("company_domain",$mt->domain)->get();
            
            if($available_format_for_domain->count() > 0){
                $matched_contact_id = $mt->id;
                $first_name = strtolower($mt->first_name);
                $last_name = strtolower($mt->last_name);
                $first_name_first_char = substr($first_name, 0, 1);
                $last_name_first_char = substr($last_name, 0, 1);
                $first_name_first_two_char = substr($first_name, 0, 2);
                $last_name_first_two_char = substr($last_name, 0, 2);
                $domain = strtolower($mt->domain);
                $vars = array(
                UtilConstant::FIRST_NAME => $first_name,
                UtilConstant::LAST_NAME => $last_name,
                UtilConstant::FIRST_NAME_FIRST_CHARACTER => $first_name_first_char,
                UtilConstant::LAST_NAME_FIRST_CHARACTER => $last_name_first_char,
                UtilConstant::FIRST_NAME_FIRST_TWO_CHARACTER => $first_name_first_two_char,
                UtilConstant::LAST_NAME_FIRST_TWO_CHARACTER => $last_name_first_two_char,
                UtilConstant::DOMAIN => $domain
                );
                UtilDebug::print_r_array("vars", $vars);
                $email_created_status = false;
                foreach ($available_format_for_domain AS $av){
                    $email_format = $av->email_format;
                    $email_format =  "$email_format";
                    $email =  str_replace("'","",strtr($email_format, $vars));
                    $email_already_exist = Emails::where('matched_contact_id',$matched_contact_id)->where('email',$email)->count();
                    if($email_already_exist == 0){
                        $newemail = new Emails();
                        $newemail->matched_contact_id = $matched_contact_id;
                        $newemail->email = $email;
                        $newemail->status = "success";
                        $newemail->save();
                        $email_created_status = true;
                    }
                }
                if($email_created_status){
                    $mt->email_status = "created";
                    $mt->save();
                }
            }
        }
    }
}
