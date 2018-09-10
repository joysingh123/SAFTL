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
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('upload_max_filesize', -1);
        ini_set('post_max_size ', -1);
        ini_set('mysql.connect_timeout', 300);
        ini_set('default_socket_timeout', 300);
        $this->validate($request, array(
            'file' => 'required'
        ));
        if ($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {
                $path = $request->file->getRealPath();
                $data = Excel::load($path, function($reader) {
                            
                        })->get();
                if (!empty($data) && $data->count()) {
                    $duplicate_in_sheet = 0;
                    $already_exist_in_db = 0;
                    $inserted = 0;
                    $domain_not_exist = 0;
                    $junk_count = 0;
                    $junk_data_array = array();
                    $domain_not_found = array();
                    $insert = array();
                    $duplicate = array();
                    foreach ($data as $key => $value) {
                        if (in_array($value, $duplicate)) {
                            $duplicate_in_sheet ++;
                        } else {
                            $duplicate[] = $value;
                            if (!UtilString::contains($value, "\u")) {
                                if ((isset($value->company_domain) && isset($value->linkedin_id)) && (UtilString::contains($value->company_domain, ".") && $value->linkedin_id > 0)) {
                                    $linkedin_id = ($value->linkedin_id != "") ? $value->linkedin_id : "";
                                    $linkedin_url = ($value->linkedin_url != "") ? $value->linkedin_url : "";
                                    $company_domain = ($value->company_domain != "") ? $value->company_domain : "";
                                    $company_name = ($value->company_name != "") ? $value->company_name : "";
                                    $company_type = ($value->company_type != "") ? $value->company_type : "";
                                    $employee_count_at_linkedin = ($value->employee_count_at_linkedin != "") ? (int) $value->employee_count_at_linkedin : 0;
                                    $industry = ($value->industry != "") ? $value->industry : "";
                                    $city = ($value->city != "") ? $value->city : "";
                                    $postal_code = ($value->postal_code != "") ? $value->postal_code : "";
                                    $employee_size = ($value->employee_size != "") ? $value->employee_size : "";
                                    $country = ($value->country != "") ? $value->country : "";
                                    $linkedin_url = UtilString::clean_string($linkedin_url);
                                    $company_domain = UtilString::clean_string($company_domain);
                                    $company_domain = UtilString::get_domain_from_url($company_domain);
                                    $company_name = UtilString::clean_string($company_name);
                                    $industry = UtilString::clean_string($industry);
                                    $city = UtilString::clean_string($city);
                                    $employee_size = UtilString::clean_string($employee_size);
                                    $country = UtilString::clean_string($country);
                                    $contact_exist = CompaniesWithDomain::where('linkedin_id', $linkedin_id)->where('company_domain', $company_domain)->count();
                                    if ($contact_exist == 0) {
                                        $insert_array = [
                                            'linkedin_id' => $linkedin_id,
                                            'linkedin_url' => $linkedin_url,
                                            'company_domain' => $company_domain,
                                            'company_name' => $company_name,
                                            'company_type' => $company_type,
                                            'employee_count_at_linkedin' => $employee_count_at_linkedin,
                                            'industry' => $industry,
                                            'city' => $city,
                                            'postal_code' => $postal_code,
                                            'employee_size' => $employee_size,
                                            'country' => $country
                                        ];
                                        $insert[] = $insert_array;
                                        $inserted ++;
                                    } else {
                                        $already_exist_in_db ++;
                                    }
                                } else {
                                    $domain_not_exist ++;
                                    $domain_not_found[] = [
                                        'linkedin_id' => $value->linkedin_id,
                                        'linkedin_url' => $value->linkedin_url,
                                        'company_domain' => $value->company_domain,
                                        'company_name' => $value->company_name,
                                        'company_type' => $value->company_type,
                                        'employee_count_at_linkedin' => $value->employee_count_at_linkedin,
                                        'industry' => $value->industry,
                                        'city' => $value->city,
                                        'postal_code' => $value->postal_code,
                                        'employee_size' => $value->employee_size,
                                        'country' => $value->country
                                    ];
                                }
                            } else {
                                $junk_count ++;
                                $junk_data_array[] = [
                                    'linkedin_id' => $value->linkedin_id,
                                    'linkedin_url' => $value->linkedin_url,
                                    'company_domain' => $value->company_domain,
                                    'company_name' => $value->company_name,
                                    'company_type' => $value->company_type,
                                    'employee_count_at_linkedin' => $value->employee_count_at_linkedin,
                                    'industry' => $value->industry,
                                    'city' => $value->city,
                                    'postal_code' => $value->postal_code,
                                    'employee_size' => $value->employee_size,
                                    'country' => $value->country
                                ];
                            }
                        }
                    }
                    if (!empty($insert)) {
                        $insertData = DB::table('companies_with_domain')->insert($insert);
                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
                        } else {
                            Session::flash('error', 'Error inserting the data..');
                            return back();
                        }
                    }
                }
                $stats_data = array(
                    "inserted" => $inserted,
                    "duplicate_in_sheet" => $duplicate_in_sheet,
                    "already_exist_in_db" => $already_exist_in_db,
                    "domain_not_exist" => $domain_not_exist,
                    "junk_count" => $junk_count,
                    "domain_not_found" => $domain_not_found,
                    "junk_data_array" => $junk_data_array
                );
                Session::flash('stats_data', $stats_data);
                return back();
            } else {
                Session::flash('error', 'File is a ' . $extension . ' file.!! Please upload a valid xls file..!!');
                return back();
            }
        }
    }

    public function importContactData(Request $request) {
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('upload_max_filesize', -1);
        ini_set('post_max_size ', -1);
        ini_set('mysql.connect_timeout', 300);
        ini_set('default_socket_timeout', 300);
        $this->validate($request, array(
            'file' => 'required'
        ));
        if ($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {
                $path = $request->file->getRealPath();
                $data = Excel::load($path, function($reader) {
                            
                        })->get();
                if (!empty($data) && $data->count()) {
                    $duplicate = 0;
                    $duplicate_in_sheet = 0;
                    $inserted = 0;
                    $campaign_id_not_exist = 0;
                    $invalid_name = 0;
                    $invalid_record = 0;
                    $invalid_array = array();
                    $duplicate_array = array();
                    foreach ($data as $key => $value) {
                        if (in_array($value, $duplicate_array)) {
                            $duplicate_in_sheet ++;
                        } else {
                            $duplicate_array[] = strtolower($value);
                            if (!UtilString::contains($value, "\u")) {
                                if (!UtilString::is_empty_string($value->full_name) && (isset($value->linkedin_id) && $value->linkedin_id > 0)) {
                                    $linkedin_id = $value->linkedin_id;
                                    $full_name = trim($value->full_name);
                                    $job_title = ($value->title != "") ? trim($value->title) : "";
                                    $company_name = ($value->company != "") ? trim($value->company) : "";
                                    $experience = ($value->experience != "") ? trim($value->experience) : "";
                                    $location = ($value->location != "") ? trim($value->location) : "";
                                    $profile_link = ($value->profile_link != "") ? $value->profile_link : "";
                                    $tag = ($value->tag != "") ? $value->tag : "";
                                    $title_level = ($value->title_level != "") ? $value->title_level : "";
                                    $department = ($value->department != "") ? $value->department : "";
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
                                    if ($linkedin_id <= 0) {
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
                                            'linkedin_id' => $linkedin_id,
                                            'full_name' => $full_name,
                                            'first_name' => $first_name,
                                            'last_name' => $last_name,
                                            'company_name' => $company_name,
                                            'job_title' => $job_title,
                                            'experience' => $experience,
                                            'location' => $location,
                                            'profile_link' => $profile_link,
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
                        "duplicate_in_sheet" => $duplicate_in_sheet,
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
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('upload_max_filesize', -1);
        ini_set('post_max_size ', -1);
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
                    $new_insert = 0;
                    $already_exist = 0;
                    $already_exist_in_sheet = 0;
                    $invalid = 0;
                    $emails_not_load = array();
                    $duplicate_array = array();
                    foreach ($data as $key => $value) {
                        if (in_array($value, $duplicate_array)) {
                            $already_exist_in_sheet ++;
                        } else {
                            $duplicate_array[] = $value;
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
                        "duplicate_in_sheet" => $already_exist_in_sheet,
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
