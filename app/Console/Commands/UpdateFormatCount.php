<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Helpers\UtilConstant;
use App\Helpers\UtilString;
use App\AvailableEmail;
use App\EmailFormat;
class UpdateFormatCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:formatcount';

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
    public function handle()
    {
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        UtilDebug::debug("start processing");
        $available_email = AvailableEmail::distinct()->select('company_domain')->orderBy('id')->groupBy('company_domain')->get();
        if($available_email->count() > 0){
            foreach($available_email AS $av){
                $company_domain = $av->company_domain;
                $available_email_record = AvailableEmail::where('company_domain',$company_domain)->get();
                $email_formats = array();
                if($available_email_record->count() > 0){
                    foreach ($available_email_record AS $aer){
                        $first_name = strtolower(trim($aer->first_name));
                        $last_name = strtolower(trim($aer->last_name));
                        $first_name_first_char = substr($first_name, 0, 1);
                        $last_name_first_char = substr($last_name, 0, 1);
                        $first_name_first_two_char = substr($first_name, 0, 2);
                        $last_name_first_two_char = substr($last_name, 0, 2);
                        $company_domain = strtolower(trim($company_domain));
                        $email = strtolower($aer->email);
                        $email_format = "";
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
                                        if(substr($email_format, 0,1) != "."){
                                            $email_format .= "@" . UtilConstant::DOMAIN;
                                        }else{
                                            $email_format = "";
                                        }
                                    }else{
                                        $email_format = "";
                                    }
                                }
                            }
                        }
                        if (strlen($email_format) > 0) {
                            if(array_key_exists($email_format, $email_formats)){
                                $email_formats[$email_format] = $email_formats[$email_format] + 1;
                            }else{
                                $email_formats[$email_format] =  1;
                            }
                        }  
                    }
                    if(count($email_formats) > 0){
                        foreach($email_formats AS $k=>$v){
                            EmailFormat::where("company_domain",$company_domain)->where("email_format",$k)->update(["format_count"=>$v]);
                        }
                        UtilDebug::print_r_array($company_domain, $email_formats);
                    }
                }
            }
        }
        UtilDebug::debug("End processing");
    }
}
