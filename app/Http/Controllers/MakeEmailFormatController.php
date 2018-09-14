<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AvailableEmail;
use App\Helpers\UtilString;
use App\Helpers\UtilDebug;
use App\Helpers\UtilConstant;
use App\EmailFormat;
use App\MatchedContact;
use DB;
use Session;

class MakeEmailFormatController extends Controller {

    public function index(Request $request) {
        $email_data = AvailableEmail::where('status', '')->take(2000)->get();
        if($email_data->count() > 0){
            foreach ($email_data AS $d){
                UtilDebug::print_r_array("$d", $this->getEmailFormate($d));
            }
        }else{
            UtilDebug::print_message("Sorry", "No Email Found For Processing.");
        }
    }

    public function getEmailFormate($data) {
        $response = array();
        $email = strtolower($data->email);
        $company_domain = trim($data->company_domain);
        $first_name = strtolower(trim($data->first_name));
        $last_name = strtolower(trim($data->last_name));
        $first_name_first_char = substr($first_name, 0, 1);
        $last_name_first_char = substr($last_name, 0, 1);
        $first_name_first_two_char = substr($first_name, 0, 2);
        $last_name_first_two_char = substr($last_name, 0, 2);
        $email_format = "";
        if (UtilString::contains($email, '@')) {
            $email_array = explode("@", $email);
            if (count($email_array) > 0) {
                $email_first_part = (isset($email_array[0])) ? $email_array[0] : "";
                $email_first_part_info = UtilString::explode_email_string($email_first_part);
                if (is_array($email_first_part_info)) {
//                    UtilDebug::print_r_array("$data->id", $email_first_part_info);
                    
                    
                    // first part processing
                    $first_part = (isset($email_first_part_info['explode_data'][0])) ? strtolower(trim($email_first_part_info['explode_data'][0])) : "";
                    if(!UtilString::is_empty_string($first_part)){
                        if(strlen($first_part) == 1){
                            if($first_part == $first_name_first_char){
                                $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                            }else if($first_part == $last_name_first_char){
                                $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER;
                            }
                        }else{
                            if($first_part == $first_name){
                                $email_format .= UtilConstant::FIRST_NAME;
                            }else if($first_part == $last_name){
                                $email_format .= UtilConstant::LAST_NAME;
                            }else if($first_part == $first_name_first_two_char){
                                $email_format .= UtilConstant::FIRST_NAME_FIRST_TWO_CHARACTER;
                            }else if($first_part == $last_name_first_two_char){
                                $email_format .= UtilConstant::LAST_NAME_FIRST_TWO_CHARACTER;
                            }
                        }
                    }
                    //explode processing
                    
                    $explode_by = (isset($email_first_part_info['explode_by'])) ? $email_first_part_info['explode_by'] : "";
                    if($explode_by != "" && $email_format != ""){
                        $email_format .= $explode_by;
                    }
                    
                    // second part processing
                    $second_part = (isset($email_first_part_info['explode_data'][1])) ? strtolower(trim($email_first_part_info['explode_data'][1])) : "";
                    if(!UtilString::is_empty_string($first_part)){
                        if(strlen($second_part) == 1){
                            if($second_part == $first_name_first_char){
                                $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                            }else if($second_part == $last_name_first_char){
                                $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER;
                            }
                        }else{
                            if($second_part == $first_name){
                                $email_format .= UtilConstant::FIRST_NAME;
                            }else if($second_part == $last_name){
                                $email_format .= UtilConstant::LAST_NAME;
                            }else if($second_part == $first_name_first_two_char){
                                $email_format .= UtilConstant::FIRST_NAME_FIRST_TWO_CHARACTER;
                            }else if($second_part == $last_name_first_two_char){
                                $email_format .= UtilConstant::LAST_NAME_FIRST_TWO_CHARACTER;
                            }
                        }
                    }
                } else {
//                    UtilDebug::print_r_array("$data", $email_first_part_info);
                    if (UtilString::contains($email_first_part_info, $first_name) && UtilString::contains($email_first_part_info, $last_name)) {
//                        UtilDebug::print_r_array("$data", $email_first_part_info);
                        $str_pos = stripos($email_first_part_info, $first_name);
                        if ($str_pos > 0) {
                            $email_format .= UtilConstant::LAST_NAME . UtilConstant::FIRST_NAME;
                        } else {
                            $email_format .= UtilConstant::FIRST_NAME . UtilConstant::LAST_NAME;
                        }
                    }
                    if (UtilString::contains($email_first_part_info, $first_name) && !UtilString::contains($email_first_part_info, $last_name)) {
//                        UtilDebug::print_r_array("$data", $email_first_part_info);
                        $replace_str = str_replace($first_name, "", $email_first_part_info);
                        $str_pos = stripos($email_first_part_info, $first_name);
                        $replace_str_len = strlen($replace_str);
                        if ($replace_str_len == 0) {
                            $email_format .= UtilConstant::FIRST_NAME;
                        } else {
                            if ($replace_str == $last_name_first_char && $str_pos > 0) {
                                $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER . UtilConstant::FIRST_NAME;
                            } else if ($replace_str == $last_name_first_char && $str_pos <= 0) {
                                $email_format .= UtilConstant::FIRST_NAME . UtilConstant::LAST_NAME_FIRST_CHARACTER;
                            }
                        }
                    }
                    if (UtilString::contains($email_first_part_info, $last_name) && !UtilString::contains($email_first_part_info, $first_name)) {
//                        UtilDebug::print_r_array("$data", $email_first_part_info);
                        $replace_str = str_replace($last_name, "", $email_first_part_info);
                        $str_pos = stripos($email_first_part_info, $last_name);
                        $replace_str_len = strlen($replace_str);
                        if ($replace_str_len == 0) {
                            $email_format .= UtilConstant::LAST_NAME;
                        } else {
                            if ($replace_str == $first_name_first_char && $str_pos > 0) {
                                $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER . UtilConstant::LAST_NAME;
                            } else if ($replace_str == $first_name_first_char && $str_pos <= 0) {
                                $email_format .= UtilConstant::LAST_NAME . UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                            }
                        }
                    }
                }
                $email_second_part = (isset($email_array[1])) ? $email_array[1] : "";
                if ($company_domain == trim($email_second_part) && strlen($email_format) > 0) {
                    $email_format .= "@" . UtilConstant::DOMAIN;
                }
            }
            if (strlen($email_format) > 0) {
                $response['status']="success";
                $response['Message'] = "formate available";
                $response['formate'] = $email_format;
                $company_domain = $data->company_domain;
                $formate_exist = EmailFormat::where('company_domain', $company_domain)->where('email_format', $email_format)->count();
                if($formate_exist == 0){
                        $insert[] = [
                                    'sample_email' => $data->email,
                                    'first_name' => $data->first_name,
                                    'last_name' => $data->last_name,
                                    'company_domain' => $company_domain,
                                    'email_format' => "$email_format",
                                    'status' => "success"
                                ];
                        $insertData = DB::table('email_format')->insert($insert);
                        $matched_contact = MatchedContact::where('domain',$data->company_domain)->where('email_format_available','no')->get();
                        if($matched_contact->count() > 0){
                            $matched_contact = $matched_contact->first();
                            $matched_contact->email_format_available = 'yes';
                            $matched_contact->save();
                        }
                        if ($insertData) {
                            $response['Message'] = "Inserted";
                            $data->status = "Email  Format Created";
                            $data->save();
                        } else {
                           $response['Message'] = "Error inserting the data..";
                        }
                        
                }else{
                    $data->status = "Email  Format Created";
                    $data->save();
                    $response['Message'] = "Already Exist";
                }
                return $email_format;
            }else{
                $response['status']="fail";
                $data->status = "Email Format Not Found";
                $data->save();
                $response['Message'] = "Email Format Not Found";
            }
        } else {
            $response['status']="fail";
            $response['Message'] = "Invalid Email";
        }
        return $response;
    }
}
