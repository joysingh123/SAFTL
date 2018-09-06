<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompaniesWithDomain;
use App\Contacts;
use File;
use Excel;
use DB;
use Session;
use App\Helpers\UtilString;
use App\Helpers\UtilConstant;
use App\AvailableEmail;
use App\Helpers\UtilDebug;

class ImportDataController extends Controller {

    public function importComapniesWithDomainView() {
        return view('addcompanieswithdomain');
    }

    public function importContactView() {
        return view('addcompaniescontact');
    }

    public function importEmailView() {
        return view('addemaildata');
    }

    public function importComapniesWithDomainData(Request $request) {
        dd($request->input());
//        ini_set('max_execution_time', 600);
//        ini_set('memory_limit', -1);
//        ini_set('mysql.connect_timeout', 300);
//        ini_set('default_socket_timeout', 300);
//        $this->validate($request, array(
//            'file' => 'required'
//        ));
//        
//        if ($request->hasFile('file')) {
//            $extension = File::extension($request->file->getClientOriginalName());
//            if ($extension == "xlsx" || $extension == "xls") {
//                $path = $request->file->getRealPath();
//                $data = Excel::load($path, function($reader) {})->get();
//                echo $data;
//                
//            } else {
//                Session::flash('error', 'File is a ' . $extension . ' file.!! Please upload a valid xls file..!!');
//                return back();
//            }
//        }
    }

    public function importContactData(Request $request) {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 300);
        ini_set('default_socket_timeout', 300);
        $this->validate($request, array(
            'file' => 'required'
        ));
        if ($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {
                $path = $request->file->getRealPath();
                $data = Excel::load($path, function($reader) {})->get();
                if (!empty($data) && $data->count()) {
                    $duplicate = 0;
                    $inserted = 0;
                    $campaign_id_not_exist = 0;
                    $invalid_name = 0;
                    $invalid_record = 0;
                    $invalid_array = array();
                    foreach ($data as $key => $value) {
                        if (!UtilString::contains($value, "\u")) {
                            if (!UtilString::is_empty_string($value->full_name) && !UtilString::is_empty_string($value->company_url)) {
                                $company_id = UtilString::get_company_id_from_url($value->company_url);
                                $full_name = trim($value->full_name);
                                $job_title = ($value->title != "") ? trim($value->title) : "";
                                $company_name = ($value->company != "") ? trim($value->company) : "";
                                $experience = ($value->experience != "") ? trim($value->experience) : "";
                                $location = ($value->location != "") ? trim($value->location) : "";
                                $profile_link = ($value->profile_link != "") ? $value->profile_link : "";
                                $tag = ($value->tag != "") ? $value->tag : "";
                                $title_level = ($value->title_level != "") ? $value->title_level : "";
                                $department = ($value->department != "") ? $value->department : "";
                                $company_url = ($company_id > 0) ? "https://www.linkedin.com/company/$company_id/" : "";
                                $first_name = "";
                                $last_name = "";
                                $status = "invalid";

                                //logic for first name and last name
                                $explode_name = explode(" ", $value->full_name);
                                if (count($explode_name) == 1) {
                                    $first_name = $explode_name[0];
                                    $status = "valid";
                                } else if (count($explode_name) == 2) {
                                    $first_name = $explode_name[0];
                                    $last_name = $explode_name[1];
                                    $status = "valid";
                                } else {
                                    $invalid_name ++;
                                }
                                if ($company_id <= 0) {
                                    $status = "invalid";
                                    $campaign_id_not_exist ++;
                                }
                                try {
                                    $contact_exist = Contacts::where('full_name', $full_name)->where('job_title', $job_title)->where('company_name', $company_name)->count();
                                } catch (\Illuminate\Database\QueryException $ex) {
                                    $contact_exist = 0;
                                    $invalid_array[] = $value;
                                    $invalid_record ++;
                                }
                                if ($contact_exist == 0) {
                                    $inserted ++;
                                    $insert[] = [
                                        'full_name' => $full_name,
                                        'first_name' => $first_name,
                                        'last_name' => $last_name,
                                        'company_name' => $company_name,
                                        'job_title' => $job_title,
                                        'experience' => $experience,
                                        'location' => $location,
                                        'profile_link' => $profile_link,
                                        'company_url' => $company_url,
                                        'tag' => $tag,
                                        'title_level' => $title_level,
                                        'department' => $department,
                                        'status' => $status,
                                    ];
                                } else {
                                    $duplicate ++;
                                }
                            }
                        } else {
                            $invalid_record ++;
                            $invalid_array[] = $value;
                        }
                    }
                    if (!empty($insert)) {
                        $insertData = DB::table('contacts')->insert($insert);
                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
                        } else {
                            Session::flash('error', 'Error inserting the data..');
                            return back();
                        }
                    }
                    $stats_data = array(
                        "duplicate" => $duplicate,
                        "inserted" => $inserted,
                        "campaign_id_not_exist" => $campaign_id_not_exist,
                        "invalid_name" => $invalid_name,
                        "invalid_record" => $invalid_record,
                        "invalid_array" => $invalid_array
                    );
                    Session::flash('stats_data', $stats_data);
                    return back();
                }
            } else {
                Session::flash('error', 'File is a ' . $extension . ' file.!! Please upload a valid xls file..!!');
                return back();
            }
        }
    }

    public function importEmailData(Request $request) {
        $this->validate($request, array(
            'file' => 'required'
        ));
        if ($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {
                $path = $request->file->getRealPath();
                $data = Excel::load($path, function($reader) {})->take(500)->get();
                if (!empty($data) && $data->count()) {
                    $new_insert = 0;
                    $already_exist = 0;
                    $invalid = 0;
                    $emails_not_load = array();
                    foreach ($data as $key => $value) {
                        if (UtilString::is_email($value->email) && !UtilString::contains($value->email, "@gmail.com") && !UtilString::is_empty_string(UtilString::trim_string($value->first_name)) && !UtilString::is_empty_string(UtilString::trim_string($value->last_name))) {
                            $email_exist = AvailableEmail::where('email', $value->email)->count();
                            if ($email_exist == 0) {
                                $insert[] = [
                                    'email' => $value->email,
                                    'company_name' => (UtilString::is_empty_string($value->company_name)) ? "" : $value->company_name,
                                    'company_domain' => (UtilString::is_empty_string($value->domain)) ? "" : $value->domain,
                                    'first_name' => (UtilString::is_empty_string($value->first_name)) ? "" : $value->first_name,
                                    'last_name' => (UtilString::is_empty_string($value->last_name)) ? "" : $value->last_name,
                                    'country' => (UtilString::is_empty_string($value->country)) ? "" : $value->country,
                                    'job_title' => (UtilString::is_empty_string($value->job_title)) ? "" : strip_tags($value->job_title),
                                    'status' => ""
                                ];
                                $new_insert ++;
                            } else {
                                $already_exist ++;
                            }
                        } else {
                            $invalid ++;
                            $emails_not_load[] = $value;
                        }
                    }
                    if (!empty($insert)) {
                        $insertData = DB::table('available_email')->insert($insert);
                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
                        } else {
                            Session::flash('error', 'Error inserting the data..');
                            return back();
                        }
                    }
                    $stats_data = array(
                        "duplicate" => $already_exist,
                        "inserted" => $new_insert,
                        "invalid_email" => $invalid,
                        "emails_not_load" => $emails_not_load
                    );
                    Session::flash('stats_data', $stats_data);
                    return back();
                }
            }
        }
    }

}
