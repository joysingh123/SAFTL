<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AvailableEmail;
use App\Helpers\UtilString;
use App\Helpers\UtilDebug;
use App\Helpers\UtilConstant;

class MakeEmailFormatController extends Controller {

    public function index(Request $request) {
        $email_data = AvailableEmail::where('status', '')->take(50)->get();
        if($email_data->count() > 0){
            foreach ($email_data AS $d){
                UtilDebug::print_message("$d", $this->getEmailFormate($d));
            }
        }else{
            UtilDebug::print_message("Sorry", "No Email Found For Processing.");
        }
    }

    public function getEmailFormate($data) {
        $email = strtolower($data->email);
        $company_domain = $data->company_domain;
        $first_name = strtolower($data->first_name);
        $last_name = strtolower($data->last_name);
        $first_name_first_char = substr($first_name, 0, 1);
        $last_name_first_char = substr($last_name, 0, 1);
        $email_format = "";
        if (UtilString::contains($email, '@')) {
            $email_array = explode("@", $email);
//            UtilDebug::print_r_array("email_array", $email_array);
            if (count($email_array) > 0) {
                $email_first_part = (isset($email_array[0])) ? $email_array[0] : "";
                $email_first_part_info = UtilString::explode_email_string($email_first_part);
                if (is_array($email_first_part_info)) {
                    if ($email_first_part_info['explode_data'][0] == $first_name && $email_first_part_info['explode_data'][1] == $last_name) {
                        $explode_sign = $email_first_part_info['explode_by'];
                        $email_format .= UtilConstant::FIRST_NAME . $explode_sign . UtilConstant::LAST_NAME;
                    }
                    if ($email_first_part_info['explode_data'][0] == $first_name && $email_first_part_info['explode_data'][1] != $last_name) {
                        $explode_sign = $email_first_part_info['explode_by'];
                        $email_format .= UtilConstant::FIRST_NAME . $explode_sign . UtilConstant::LAST_NAME;
                    }
                    if ($email_first_part_info['explode_data'][0] == $last_name && $email_first_part_info['explode_data'][1] == $first_name) {
                        $explode_sign = $email_first_part_info['explode_by'];
                        $email_format .= UtilConstant::LAST_NAME . $explode_sign . UtilConstant::FIRST_NAME;
                    }
                    if ($email_first_part_info['explode_data'][0] == $last_name_first_char && $email_first_part_info['explode_data'][1] == $first_name) {
                        $explode_sign = $email_first_part_info['explode_by'];
                        $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER . $explode_sign . UtilConstant::FIRST_NAME;
                    }
                    if ($email_first_part_info['explode_data'][0] == $first_name_first_char && $email_first_part_info['explode_data'][1] == $last_name) {
                        $explode_sign = $email_first_part_info['explode_by'];
                        $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER . $explode_sign . UtilConstant::LAST_NAME;
                    }
                } else {
                    if (UtilString::contains($email_first_part_info, $first_name) && UtilString::contains($email_first_part_info, $last_name)) {
                        $str_pos = stripos($email_first_part_info, $first_name);
                        if ($str_pos > 0) {
                            $email_format .= UtilConstant::LAST_NAME . UtilConstant::FIRST_NAME;
                        } else {
                            $email_format .= UtilConstant::FIRST_NAME . UtilConstant::LAST_NAME;
                        }
                    }
                    if (UtilString::contains($email_first_part_info, $first_name) && !UtilString::contains($email_first_part_info, $last_name)) {
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
                    if (!UtilString::contains($email_first_part_info, $first_name) && UtilString::contains($email_first_part_info, $last_name)) {
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
                return $email_format;
            }else{
               return "Email Format Not Found"; 
            }
        } else {
            return "Invalid Email";
        }
    }

}
