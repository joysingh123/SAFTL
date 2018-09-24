<?php

namespace App\Traits;

use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\Helpers\UtilConstant;
use App\EmailFormat;
use App\MatchedContact;
use App\Emails;

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
            $email_already_exist = 0;
            foreach ($matched_contact AS $mt) {
                $available_format_for_domain = EmailFormat::where("company_domain", $mt->domain)->orderBY('format_percentage', 'DESC')->take(2)->get();
                
                if ($available_format_for_domain->count() > 0) {
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
                    $email_created_status = false;
                    $data = $available_format_for_domain->pluck('format_percentage')->values();
                    $process_data = [];
                    if (count($data) == 1) {
                        $process_data = $available_format_for_domain;
                    }
                    if (count($data) == 2) {
                        $process_count = $data[0] - $data[1];
                        if ($process_count <= 40) {
                            $process_data = $available_format_for_domain;
                        } else {
//                            UtilDebug::print_message("process_data in 1", $available_format_for_domain);
                            $process_data = $available_format_for_domain->forget(1);
//                            UtilDebug::print_message("process_data in 2", $process_data);
                        }
                    }
                    foreach ($process_data AS $av) {
                        $email_format = $av->email_format;
                        $email_format = "$email_format";
                        $email = str_replace("'", "", strtr($email_format, $vars));
                        $email_already_exist = Emails::where('email', $email)->count();
                        if ($email_already_exist == 0) {
                            $newemail = new Emails();
                            $newemail->matched_contact_id = $matched_contact_id;
                            $newemail->email = $email;
                            $newemail->format_percentage = $av->format_percentage;
                            $newemail->status = "success";
                            $newemail->save();
                            $email_created_status = true;
                        } else {
                            $email_created_status = true;
                            $email_already_exist ++;
                        }
                    }
                    if ($email_created_status) {
                        $email_created ++;
                        $mt->email_status = "created";
                        $mt->save();
                    }
                }
            }
            $response['status'] = "success";
            $response['data'] = array("Total" => $total, "Email Created" => $email_created, "Email Already Exist" => $email_already_exist);
        } else {
            $response['status'] = "fail";
            $response['data'] = "No, Contact Found For Email Creation";
        }
        return $response;
    }
}
?>
