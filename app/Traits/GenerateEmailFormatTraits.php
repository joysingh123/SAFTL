<?php

namespace App\Traits;

use App\AvailableEmail;
use App\Helpers\UtilString;
use App\Helpers\UtilDebug;
use App\Helpers\UtilConstant;
use App\EmailFormat;
use App\MatchedContact;
use DB;

trait GenerateEmailFormatTraits {

    public function generateEmailFormat() {
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $response = array();
        $limit = 1500;
        $email_data = AvailableEmail::where('status', '')->take($limit)->get();
        if ($email_data->count() > 0) {
            $total = $limit;
            $new_format_created = 0;
            $already_exist = 0;
            $formate_not_found = 0;
            foreach ($email_data AS $data) {
                $email = strtolower($data->email);
                $first_name = strtolower(trim($data->first_name));
                $last_name = strtolower(trim($data->last_name));
                $first_name_first_char = substr($first_name, 0, 1);
                $last_name_first_char = substr($last_name, 0, 1);
                $first_name_first_two_char = substr($first_name, 0, 2);
                $last_name_first_two_char = substr($last_name, 0, 2);
                $email_format = "";
                $company_domain = "";
                if (UtilString::contains($email, '@')) {
                    $email_array = explode("@", $email);
                    if (count($email_array) > 0) {
                        $company_domain = $email_array[1];
                        $email_first_part = (isset($email_array[0])) ? $email_array[0] : "";
                        $email_first_part_info = UtilString::explode_email_string($email_first_part);
                        if (is_array($email_first_part_info)) {
                            // first part processing
                            $first_part = (isset($email_first_part_info['explode_data'][0])) ? strtolower(trim($email_first_part_info['explode_data'][0])) : "";
                            if (!UtilString::is_empty_string($first_part)) {
                                if (strlen($first_part) == 1) {
                                    if ($first_part == $first_name) {
                                        $email_format .= UtilConstant::FIRST_NAME;
                                    } else if ($first_part == $last_name) {
                                        $email_format .= UtilConstant::LAST_NAME;
                                    } else if ($first_part == $first_name_first_char) {
                                        $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                                    } else if ($first_part == $last_name_first_char) {
                                        $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER;
                                    }
                                } else if (strlen($first_part) == 2) {
                                    if ($first_part == $first_name) {
                                        $email_format .= UtilConstant::FIRST_NAME;
                                    } else if ($first_part == $last_name) {
                                        $email_format .= UtilConstant::LAST_NAME;
                                    }else{
                                        $char_array = str_split($first_part);
                                        $firstchar = $char_array[0];
                                        $secondchar = $char_array[1];
                                        if ($firstchar == $first_name_first_char) {
                                            $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                                        } else if ($firstchar == $last_name_first_char) {
                                            $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER;
                                        }
                                        if ($secondchar == $first_name_first_char) {
                                            $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                                        } else if ($secondchar == $last_name_first_char) {
                                            $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER;
                                        }
                                    }
                                } else {
                                    if ($first_part == $first_name) {
                                        $email_format .= UtilConstant::FIRST_NAME;
                                    } else if ($first_part == $last_name) {
                                        $email_format .= UtilConstant::LAST_NAME;
                                    }
                                }
                            }
                            //explode processing
                            $explode_by = (isset($email_first_part_info['explode_by'])) ? $email_first_part_info['explode_by'] : "";
                            if ($explode_by != "" && strlen($email_format) > 0) {
                                $email_format .= $explode_by;
                            }

                            // second part processing
                            $second_part = (isset($email_first_part_info['explode_data'][1])) ? strtolower(trim($email_first_part_info['explode_data'][1])) : "";
                            if (!UtilString::is_empty_string($first_part)) {
                                if (strlen($second_part) == 1) {
                                    if ($second_part == $first_name) {
                                        $email_format .= UtilConstant::FIRST_NAME;
                                    } else if ($second_part == $last_name) {
                                        $email_format .= UtilConstant::LAST_NAME;
                                    }else if ($second_part == $first_name_first_char) {
                                        $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                                    } else if ($second_part == $last_name_first_char) {
                                        $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER;
                                    }
                                } else if (strlen($second_part) == 2) {
                                    if ($second_part == $first_name) {
                                        $email_format .= UtilConstant::FIRST_NAME;
                                    } else if ($second_part == $last_name) {
                                        $email_format .= UtilConstant::LAST_NAME;
                                    }else{
                                        $char_array = str_split($second_part);
                                        $firstchar = $char_array[0];
                                        $secondchar = $char_array[1];
                                        if ($firstchar == $first_name_first_char) {
                                            $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                                        } else if ($firstchar == $last_name_first_char) {
                                            $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER;
                                        }
                                        if ($secondchar == $first_name_first_char) {
                                            $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                                        } else if ($secondchar == $last_name_first_char) {
                                            $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER;
                                        }
                                    }
                                } else {
                                    if ($second_part == $first_name) {
                                        $email_format .= UtilConstant::FIRST_NAME;
                                    } else if ($second_part == $last_name) {
                                        $email_format .= UtilConstant::LAST_NAME;
                                    }
                                }
                            }
                        } else {
                            if (strlen($email_first_part_info) == 1) {
                                if ($email_first_part_info == $first_name) {
                                    $email_format .= UtilConstant::FIRST_NAME;
                                } else if ($email_first_part_info == $last_name) {
                                    $email_format .= UtilConstant::LAST_NAME;
                                } else if ($email_first_part_info == $first_name_first_char) {
                                    $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                                } else if ($email_first_part_info == $last_name_first_char) {
                                    $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER;
                                }
                            } else if (strlen($email_first_part_info) == 2) {
                                if ($email_first_part_info == $first_name) {
                                    $email_format .= UtilConstant::FIRST_NAME;
                                } else if ($email_first_part_info == $last_name) {
                                    $email_format .= UtilConstant::LAST_NAME;
                                }else{
                                    $char_array = str_split($email_first_part_info);
                                    $firstchar = $char_array[0];
                                    $secondchar = $char_array[1];
                                    if ($firstchar == $first_name_first_char) {
                                        $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                                    } else if ($firstchar == $last_name_first_char) {
                                        $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER;
                                    }
                                    if ($secondchar == $first_name_first_char) {
                                        $email_format .= UtilConstant::FIRST_NAME_FIRST_CHARACTER;
                                    } else if ($secondchar == $last_name_first_char) {
                                        $email_format .= UtilConstant::LAST_NAME_FIRST_CHARACTER;
                                    }
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
                                if (UtilString::contains($email_first_part_info, $last_name) && !UtilString::contains($email_first_part_info, $first_name)) {
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
                        }
                        $email_second_part = (isset($email_array[1])) ? $email_array[1] : "";
                        if ($company_domain == trim($email_second_part) && strlen($email_format) > 0) {
                            if(substr($email_format, -1) != "."){
                                $email_format .= "@" . UtilConstant::DOMAIN;
                            }else{
                                $email_format = "";
                            }
                        }
                    }
                    if (strlen($email_format) > 0) {
                        $formate_exist = EmailFormat::where('company_domain', $company_domain)->where('email_format', $email_format)->get();
                        $total_in_available_email = AvailableEmail::where('email', 'LIKE', "%@$company_domain%")->count();
                        if ($formate_exist->count() == 0) {
                            $insert = [
                                'sample_email' => $data->email,
                                'first_name' => $data->first_name,
                                'last_name' => $data->last_name,
                                'company_domain' => $company_domain,
                                'email_format' => $email_format,
                                'format_count' => 1,
                                'available_email_count' => $total_in_available_email,
                                'source' => 'available_email',
                                'status' => "success"
                            ];
                            $insertData = DB::table('email_format')->insert($insert);
                            $matched_contact = MatchedContact::where('domain', $company_domain)->where('email_format_available', 'no')->count();
                            if ($matched_contact > 0) {
                                MatchedContact::where('domain', $company_domain)->update(['email_status' => NULL, 'email_format_available' => 'yes']);
                            }
                            if ($insertData) {
                                $new_format_created ++;
                                EmailFormat::where('company_domain', '=', $company_domain)->update(['available_email_count' => $total_in_available_email]);
                                $data->status = "Email Format Created";
                                $data->save();
                            }
                        } else {
                            $data->status = "Email Format Created";
                            $formate_exist->first()->format_count = $formate_exist->first()->format_count + 1;
                            EmailFormat::where('company_domain', '=', $company_domain)->update(['available_email_count' => $total_in_available_email]);
                            $formate_exist->first()->save();
                            $data->save();
                            $already_exist ++;
                        }
                    } else {
                        $data->status = "Email Format Not Found";
                        $data->save();
                        $formate_not_found ++;
                    }
                } else {
                    $data->status = "Invalid Email";
                    $data->save();
                }
            }
            $response['status'] = 'stats';
            $response['stats_data'] = array("Total" => $total, 'Format_created' => $new_format_created, "Already_Exist" => $already_exist, "Format_Not_Found" => $formate_not_found);
        } else {
            $response['status'] = "fail";
            $response['Message'] = "No Email Found For Processing";
        }
        return $response;
    }
}
?>

