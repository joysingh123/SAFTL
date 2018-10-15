<?php

namespace App\Traits;

use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\Helpers\UtilConstant;
use App\EmailFormat;
use App\MatchedContact;
use App\Emails;
use App\AvailableEmail;
use App\BounceEmail;

trait CreateEmailTraits {

    public function createEmail() {
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $response = array();
        $limit = 1500;
        $matched_contact = MatchedContact::where('email_format_available', 'yes')->whereNull('email_status')->take($limit)->get();
        if ($matched_contact->count() > 0) {
            $total = $matched_contact->count();
            $email_created = 0;
            $found_in_available_email = 0;
            $found_in_bounce = 0;
            $email_already_exist = 0;
            foreach ($matched_contact AS $mt) {
                $matched_contact_id = $mt->id;
                $first_name = strtolower($mt->first_name);
                $last_name = strtolower($mt->last_name);
                $first_name_first_char = substr($first_name, 0, 1);
                $last_name_first_char = substr($last_name, 0, 1);
                $first_name_first_two_char = substr($first_name, 0, 2);
                $last_name_first_two_char = substr($last_name, 0, 2);
                $matched_contact_domain = strtolower(trim($mt->domain));
                $exist_in_available_email = AvailableEmail::where('first_name', '=', $first_name)->where('last_name', '=', $last_name)->where('company_domain', '=', $matched_contact_domain)->get();
                if ($exist_in_available_email->count() > 0) {
                    $mt->email = $exist_in_available_email->first()->email;
                    $mt->email_status = 'valid';
                    $mt->email_validation_date = '2018-09-01 00:00:00';
                    $mt->save();
                    $found_in_available_email ++;
                } else {
                    $available_format_for_domain = EmailFormat::where("company_domain", $mt->domain)->orderBY('format_percentage', 'DESC')->take(2)->get();
                    if ($available_format_for_domain->count() > 0) {
                        if (!UtilString::is_empty_string($first_name) && !UtilString::is_empty_string($last_name)) {
                            $vars = array(
                                UtilConstant::FIRST_NAME => $first_name,
                                UtilConstant::LAST_NAME => $last_name,
                                UtilConstant::FIRST_NAME_FIRST_CHARACTER => $first_name_first_char,
                                UtilConstant::LAST_NAME_FIRST_CHARACTER => $last_name_first_char,
                                UtilConstant::FIRST_NAME_FIRST_TWO_CHARACTER => $first_name_first_two_char,
                                UtilConstant::LAST_NAME_FIRST_TWO_CHARACTER => $last_name_first_two_char,
                                UtilConstant::DOMAIN => $matched_contact_domain
                            );
                            $email_created_status = false;
                            $data = $available_format_for_domain->pluck('format_percentage')->values();
                            $process_data = [];
                            if (count($data) == 1) {
                                if ($data[0] >= 10) {
                                    $process_data = $available_format_for_domain;
                                }
                            }
                            if (count($data) == 2) {
                                if ($data[0] >= 10 && $data[1] <= 10) {
                                    $process_data = $available_format_for_domain->forget(1);
                                } else if ($data[0] > 10 && $data[1] > 10) {
                                    $process_count = $data[0] - $data[1];
                                    if ($process_count <= 40) {
                                        $process_data = $available_format_for_domain;
                                    } else {
                                        $process_data = $available_format_for_domain->forget(1);
                                    }
                                }
                            }
                            if (count($process_data) > 0) {
                                $is_bounce = false;
                                foreach ($process_data AS $av) {
                                    $email_format = $av->email_format;
                                    $email_format = "$email_format";
                                    $email = str_replace("'", "", strtr($email_format, $vars));
                                    if (UtilString::is_email($email)) {
                                        $email_already_exist = Emails::where('email', $email)->count();
                                        if ($email_already_exist == 0) {
                                            $is_exist_in_bounce = BounceEmail::where('email','=',$email)->get();
                                            if($is_exist_in_bounce->count() > 0){
                                                $is_bounce = true;
                                            }else{
                                                $newemail = new Emails();
                                                $newemail->matched_contact_id = $matched_contact_id;
                                                $newemail->email = trim($email);
                                                $newemail->format_percentage = $av->format_percentage;
                                                $newemail->status = "success";
                                                $newemail->save();
                                                $email_created_status = true;
                                                $is_bounce = false;
                                            }
                                        } else {
                                            $email_already_exist ++;
                                        }
                                    }
                                }
                                if ($email_created_status) {
                                    $email_created ++;
                                    $mt->email_status = "created";
                                    $mt->save();
                                }else if($is_bounce){
                                    $found_in_bounce ++;
                                    $mt->email_status = "bounce";
                                    $mt->save();
                                }else{
                                    $mt->email_status = "unrecognized";
                                    $mt->save();
                                }
                            }
                        }else{
                            $email_status = (UtilString::is_empty_string($last_name)) ? 'last_name not found' : 'unrecognized';
                            $mt->email_status = $email_status;
                            $mt->save();
                        }
                    }
                }
            }
            $response['status'] = "success";
            $response['data'] = array("Total" => $total, "Email Created" => $email_created,"In Available Email"=>$found_in_available_email,"Found In Bounce"=>$found_in_bounce,"Email Already Exist" => $email_already_exist);
        } else {
            $response['status'] = "fail";
            $response['data'] = "No, Contact Found For Email Creation";
        }
        return $response;
    }
}
?>
