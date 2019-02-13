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
use App\BounceEmail;
use App\EmailData;
use App\EmailForValidation;
use Illuminate\Support\Facades\Auth;
use App\CompaniesWithoutDomain;

class ImportDataController extends Controller {

    public function importComapniesWithDomainView() {
        return view('addcompanieswithdomain');
    }

    public function importContactView() {
        return view('addcompaniescontact');
    }

    public function importEmailDataImportView() {
        return view('addemaildataimport');
    }

    public function importEmailView() {
        return view('addemaildata');
    }

    public function importBounceEmailView() {
        return view('addbounceemail');
    }

    public function importComapniesWithDomainData(Request $request) {
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('upload_max_filesize', -1);
        ini_set('post_max_size ', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $this->validate($request, array(
            'file' => 'required'
        ));
        if ($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {
                $path = $request->file->getRealPath();
                $data = array();
                $data = Excel::load($path, function($reader) {
                            
                        })->get();
                $header = $data->getHeading();
                if (in_array('linkedin_id', $header) && in_array('linkedin_url', $header) && in_array('company_domain', $header) && in_array('company_name', $header) && in_array('company_type', $header) && in_array('industry', $header) && in_array('city', $header) && in_array('employee_size', $header)) {
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
                            if (UtilString::is_empty_string($value->company_domain) && UtilString::is_empty_string($value->linkedin_id) && UtilString::is_empty_string($value->company_name)) {
                                
                            } else {
                                if (in_array($value, $duplicate)) {
                                    $duplicate_in_sheet ++;
                                } else {
                                    $duplicate[] = $value;
                                    if (!UtilString::contains($value, "\u")) {
                                        if ((isset($value->company_domain) && isset($value->linkedin_id)) && (UtilString::contains($value->company_domain, "."))) {
                                            $linkedin_id = ($value->linkedin_id != "") ? UtilString::get_company_id_from_url($value->linkedin_id) : 0;
                                            $linkedin_url = ($value->linkedin_url != "") ? $value->linkedin_url : "";
                                            $company_domain = ($value->company_domain != "") ? $value->company_domain : "";
                                            $company_name = ($value->company_name != "") ? $value->company_name : "";
                                            $company_type = ($value->company_type != "") ? $value->company_type : "";
                                            $employee_count_at_linkedin = ($value->employee_count_at_linkedin != "") ? (int) $value->employee_count_at_linkedin : 0;
                                            $industry = ($value->industry != "") ? $value->industry : "";
                                            $city = ($value->city != "") ? $value->city : "";
                                            $postal_code = ($value->postal_code != "") ? trim($value->postal_code) : "";
                                            $employee_size = ($value->employee_size != "") ? trim($value->employee_size) : "";
                                            $country = ($value->country != "") ? trim($value->country) : "";
                                            $state = NULL;
                                            if(isset($value->state)){
                                                $state = ($value->state != "") ? trim($value->state) : NULL;
                                            }
                                            $logo_url = ($value->logo != "") ? trim($value->logo) : NULL;
                                            $facebook_url = ($value->facebook_url != "") ? trim($value->facebook_url) : NULL;
                                            $twitter_url = ($value->twitter_url != "") ? trim($value->twitter_url) : NULL;
                                            $zoominfo_url = ($value->zoominfo_url != "") ? trim($value->zoominfo_url) : NULL;
          
                                            $linkedin_url = UtilString::clean_string($linkedin_url);
                                            $company_domain = UtilString::clean_string($company_domain);
                                            $company_domain = UtilString::get_domain_from_url($company_domain);
                                            $company_name = UtilString::clean_string($company_name);
                                            $industry = UtilString::clean_string($industry);
                                            $city = UtilString::clean_string($city);
                                            $employee_size = UtilString::clean_string($employee_size);
                                            $country = UtilString::clean_string($country);
                                            $contact_exist = CompaniesWithDomain::where('linkedin_id', $linkedin_id)->count();
                                            if ($contact_exist == 0) {
                                                if (!empty($linkedin_id)) {
                                                    $insert_array = [
                                                        'user_id' => Auth::id(),
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
                                                        'country' => $country,
                                                        'state' => $state,
                                                        'logo_url' => $logo_url,
                                                        'facebook_url' => $facebook_url,
                                                        'twitter_url' => $twitter_url,
                                                        'zoominfo_url' => $zoominfo_url
                                                    ];
                                                    $insert[] = $insert_array;
                                                    $inserted ++;
                                                }
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
                                            $company_without_domain = CompaniesWithoutDomain::where('company_name',$value->company_name)->get();
                                            if($company_without_domain->count() <= 0){
                                                $company_d = new CompaniesWithoutDomain();
                                                $linkedin_id = ($value->linkedin_id != "") ? UtilString::get_company_id_from_url($value->linkedin_id) : 0;
                                                $company_d->linkedin_id = $linkedin_id;
                                                $company_d->company_domain = $value->company_domain;
                                                $company_d->company_name = $value->company_name;
                                                $company_d->employee_count_at_linkedin = $value->employee_count_at_linkedin;
                                                $company_d->industry = $value->industry;
                                                $company_d->city = $value->city;
                                                $company_d->employee_size = $value->employee_size;
                                                $company_d->country = $value->country;
                                                $company_d->state = $value->state;
                                                $company_d->logo_url = $value->logo;
                                                $company_d->facebook_url = $value->facebook_url;
                                                $company_d->twitter_url = $value->twitter_url;
                                                $company_d->zoominfo_url = $value->zoominfo_url;
                                                $company_d->save();
                                            }
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
                        }
                        if (!empty($insert)) {
                            $insert_chunk = array_chunk($insert, 100);
                            foreach ($insert_chunk AS $ic) {
                                $insertData = DB::table('companies_with_domain')->insert($ic);
                            }
                            if ($insertData) {
                                CompaniesWithDomain::where('employee_size', '0-1 employees')->update(['employee_size' => '1 to 10']);
                                CompaniesWithDomain::where('employee_size', '1,001-5,000 employees')->update(['employee_size' => '1001 to 5000']);
                                CompaniesWithDomain::where('employee_size', '1-10 employees')->update(['employee_size' => '1 to 10']);
                                CompaniesWithDomain::where('employee_size', '10')->update(['employee_size' => '1 to 10']);
                                CompaniesWithDomain::where('employee_size', '10,001+ employees')->update(['employee_size' => '10000 above']);
                                CompaniesWithDomain::where('employee_size', '10001 + Employees')->update(['employee_size' => '10000 above']);
                                CompaniesWithDomain::where('employee_size', '1001-5000 employees')->update(['employee_size' => '1001 to 5000']);
                                CompaniesWithDomain::where('employee_size', '11-50 employees')->update(['employee_size' => '11 to 50']);
                                CompaniesWithDomain::where('employee_size', '2-10 employees')->update(['employee_size' => '1 to 10']);
                                CompaniesWithDomain::where('employee_size', '201-500 employees')->update(['employee_size' => '201 to 500']);
                                CompaniesWithDomain::where('employee_size', '5,001-10,000 employees')->update(['employee_size' => '5001 to 10000']);
                                CompaniesWithDomain::where('employee_size', '5001 - 10000 employees')->update(['employee_size' => '5001 to 10000']);
                                CompaniesWithDomain::where('employee_size', '5001-10,000 employees')->update(['employee_size' => '5001 to 10000']);
                                CompaniesWithDomain::where('employee_size', '5001-10000 employees')->update(['employee_size' => '5001 to 10000']);
                                CompaniesWithDomain::where('employee_size', '501-1,000 employees')->update(['employee_size' => '501 to 1000']);
                                CompaniesWithDomain::where('employee_size', '501-1000 employees')->update(['employee_size' => '501 to 1000']);
                                CompaniesWithDomain::where('employee_size', '51-200 employees')->update(['employee_size' => '51 to 200']);
                                CompaniesWithDomain::where('employee_size', 'Myself Only')->update(['employee_size' => '1 to 10']);
                                CompaniesWithDomain::where('employee_size', 'NA')->update(['employee_size' => 'Invalid']);
                                DB::statement("update contacts A inner join companies_with_domain B on A.linkedin_id = B.linkedin_id set A.process_for_contact_match = 'not processed' where A.process_for_contact_match = 'company not found'");
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
                    Session::flash('error', "The Sheet Header contain wrong column name");
                    return back();
                }
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
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $this->validate($request, array(
            'file' => 'required'
        ));
        if ($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {
                $path = $request->file->getRealPath();
                $data = Excel::load($path, function($reader) {})->get();
                $header = $data->getHeading();
                if (in_array('full_name', $header) && in_array('first_name', $header) && in_array('last_name', $header) && in_array('company', $header) && in_array('company_url', $header) && in_array('title', $header) && in_array('experience', $header) && in_array('location', $header) && in_array('industry', $header) && in_array('profile_link', $header)) {
                    if (!empty($data) && $data->count() < 20000) {
                        $duplicate = 0;
                        $duplicate_in_sheet = 0;
                        $inserted = 0;
                        $campaign_id_not_exist = 0;
                        $invalid_name = 0;
                        $invalid_record = 0;
                        $invalid_array = array();
                        $duplicate_array = array();
//                    $contacts_data = Contacts::get(['first_name','last_name','company_name']);
                        foreach ($data as $key => $value) {
                            //echo $value;
                            if (in_array($value, $duplicate_array)) {
                                $duplicate_in_sheet ++;
                            } else {
                                $duplicate_array[] = strtolower($value);
//                            if (!UtilString::contains($value, "\u")) {
                                if (!UtilString::is_empty_string($value->company_url)) {
                                    $company_id = UtilString::get_company_id_from_url($value->company_url);
                                    $linkedin_id = $company_id;
                                    $full_name = trim($value->full_name);
                                    $job_title = ($value->title != "") ? trim(strip_tags($value->title)) : "";
                                    $company_name = ($value->company != "") ? trim(strip_tags($value->company)) : "";
                                    $experience = ($value->experience != "") ? trim(strip_tags($value->experience)) : "";
                                    $location = ($value->location != "") ? trim($value->location) : "";
                                    $contact_country = (isset($value->contact_country) && !UtilString::is_empty_string($value->contact_country)) ? trim($value->contact_country) : NULL;
                                    $profile_link = ($value->profile_link != "") ? $value->profile_link : "";
                                    $tag = ($value->tag != "") ? trim($value->tag) : "";
                                    $title_level = ($value->title_level != "") ? trim($value->title_level) : "";
                                    $department = ($value->department != "") ? trim($value->department) : "";
                                    $first_name = (isset($value->first_name)) ? trim($value->first_name) : "";
                                    $last_name = (isset($value->last_name)) ? trim($value->last_name) : "";
                                    $status = "invalid";

                                    //logic for first name and last name
                                    $insert_status = true;
                                    if (!UtilString::is_empty_string($first_name) && !UtilString::is_empty_string($last_name)) {
                                        $status = "valid";
                                    } elseif(!UtilString::is_empty_string($full_name)){
                                        $explode_name = explode(" ", $full_name);
                                        if (count($explode_name) == 1) {
                                            $first_name = $explode_name[0];
                                            $status = "valid";
                                        } else if (count($explode_name) == 2) {
                                            $first_name = $explode_name[0];
                                            $last_name = $explode_name[1];
                                            $status = "valid";
                                        } else {
                                            $insert_status = false;
                                            $invalid_name ++;
                                            $invalid_array[] = $value;
                                        }
                                    }
                                    if(UtilString::is_empty_string($first_name) && UtilString::is_empty_string($last_name)){
                                        $insert_status = false;
                                        $invalid_name ++;
                                        $invalid_array[] = $value;
                                    }
                                    if(UtilString::is_empty_string($first_name) && !UtilString::is_empty_string($last_name)){
                                        $insert_status = false;
                                        $invalid_name ++;
                                        $invalid_array[] = $value;
                                    }
                                    if(UtilString::contains($first_name, "(") || UtilString::contains($first_name, ")") || UtilString::contains($last_name, "(") || UtilString::contains($last_name, ")")){
                                        $insert_status = false;
                                        $invalid_name ++;
                                        $invalid_array[] = $value;
                                    }
                                    if(UtilString::contains($first_name, "#") || UtilString::contains($last_name, "#")){
                                        $insert_status = false;
                                        $invalid_name ++;
                                        $invalid_array[] = $value;
                                    }
                                    if(strlen($first_name) == 1 || strlen($last_name) == 1){
                                        $insert_status = false;
                                        $invalid_name ++;
                                        $invalid_array[] = $value;
                                    }
                                    if ($linkedin_id <= 0 || empty($linkedin_id)) {
                                        $status = "invalid";
                                        $insert_status = false;
                                        $campaign_id_not_exist ++;
                                    }
//                                    try {
//                                        $contact_exist = Contacts::where('full_name', $full_name)->where('job_title', $job_title)->where('company_name', $company_name)->count();
//                                    } catch (\Illuminate\Database\QueryException $ex) {
//                                        $contact_exist = 0;
//                                        $invalid_array[] = $value;
//                                        $invalid_record ++;
//                                    }
                                    if ($insert_status) {
//                                        $contact_filter_all = $contacts_data->where('first_name', $first_name)->where('last_name', $last_name)->where('company_name', $company_name);
//                                        if($contact_filter_all->count() <= 0){
                                        $inserted ++;
                                        $insert[] = [
                                            'user_id' => Auth::id(),
                                            'linkedin_id' => $linkedin_id,
                                            'full_name' => $full_name,
                                            'first_name' => $first_name,
                                            'last_name' => $last_name,
                                            'company_name' => $company_name,
                                            'job_title' => $job_title,
                                            'experience' => $experience,
                                            'location' => $location,
                                            'contact_country' => $contact_country,
                                            'profile_link' => $profile_link,
                                            'tag' => $tag,
                                            'title_level' => $title_level,
                                            'department' => $department,
                                            'status' => $status,
                                        ];
                                        $user_id = Auth::id();
                                        $experience = trim(preg_replace('/\s+/', ' ', $experience));
                                        $full_name = str_replace('"', "", $full_name);
                                        $job_title = str_replace('"', "", $job_title);
                                        $company_name = str_replace('"', "", $company_name);
                                        $insertQuery[] = "($user_id,$linkedin_id,\"$full_name\",\"$first_name\",\"$last_name\",\"$company_name\",\"$job_title\",\"$experience\",\"$location\",\"$contact_country\",\"$profile_link\",\"$status\",\"$tag\",\"$title_level\",\"$department\")";
//                                        }else{
//                                            $duplicate ++;
//                                        }
                                    }
                                }
//                            } else {
//                                $invalid_record ++;
//                                $invalid_array[] = $value;
//                            }
                            }
                        }
                        if (!empty($insertQuery)) {
                            $insert_chunk = array_chunk($insertQuery, 100);
                            foreach ($insert_chunk AS $ic) {
                                $date = date("Y-m-d H:i");
                                echo $date."<br>";
                                $string_data = implode(",", $ic);
                                $insertData = DB::statement("INSERT IGNORE INTO contacts(user_id,linkedin_id,full_name,first_name,last_name,company_name,job_title,experience,location,contact_country,profile_link,status,tag,title_level,department) VALUES $string_data");
                            }
                            if ($insertData) {
                                Session::flash('success', 'Your Data has successfully imported');
                                return back();
                            } else {
                                Session::flash('error', 'Error inserting the data..');
                                return back();
                            }
                        }
//                    if (!empty($insert)) {
//                        $insert_chunk = array_chunk($insert, 100);
//                        foreach ($insert_chunk AS $ic) {
//                            print_r($ic);
//                            $insertData = DB::table('contacts')->insertIgnore($ic);
//                            $inserted += $insertData;
//                        }
//                        
//                        if ($insertData) {
//                            Session::flash('success', 'Your Data has successfully imported');
//                        } else {
//                            Session::flash('error', 'Error inserting the data..');
//                            return back();
//                        }
//                    }
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
                    } else {
                        Session::flash('error', "The Sheet contains Only 20,000 records");
                        return back();
                    }
                } else {
                    Session::flash('error', "The Sheet Header contain wrong column name");
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
                $data = Excel::load($path, function($reader) {
                            
                        })->get();
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
                                if (!UtilString::contains($value, "\u")) {
                                    $email_exist = AvailableEmail::where('email', $value->email)->count();
                                    if ($email_exist == 0) {
                                        $email_array = explode("@", $value->email);
                                        $company_domain = trim($email_array[1]);
                                        $insert[] = [
                                            'user_id' => Auth::id(),
                                            'email' => trim($value->email),
                                            'company_name' => (UtilString::is_empty_string($value->company_name)) ? "" : trim($value->company_name),
                                            'company_domain' => (UtilString::is_empty_string($company_domain)) ? "" : $company_domain,
                                            'first_name' => (UtilString::is_empty_string($value->first_name)) ? "" : trim($value->first_name),
                                            'last_name' => (UtilString::is_empty_string($value->last_name)) ? "" : trim($value->last_name),
                                            'country' => (UtilString::is_empty_string($value->country)) ? "" : trim($value->country),
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
                            } else {
                                $invalid ++;
                                $emails_not_load[] = $value;
                            }
                        }
                    }
                    if (!empty($insert)) {
                        $insert_chunk = array_chunk($insert, 100);
                        foreach ($insert_chunk AS $ic) {
                            $insertData = DB::table('available_email')->insert($ic);
                        }
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

    public function importEmailDataDump(Request $request) {
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
                            if (UtilString::is_email($value->email) && !UtilString::contains($value->email, "@gmail.com") && !UtilString::is_empty_string(UtilString::trim_string($value->full_name))) {
                                if (!UtilString::contains($value, "\u")) {
                                    $email_exist = EmailData::where('email', $value->email)->count();
                                    if ($email_exist == 0) {
                                        $email_array = explode("@", $value->email);
                                        $company_domain = trim($email_array[1]);
                                        $insert[] = [
                                            'user_id' => Auth::id(),
                                            'email' => trim($value->email),
                                            'linkedin_id' => (UtilString::is_empty_string($value->linkedin_id)) ? 0 : trim($value->linkedin_id),
                                            'full_name' => (UtilString::is_empty_string($value->full_name)) ? "" : trim($value->full_name),
                                            'first_name' => (UtilString::is_empty_string($value->first_name)) ? "" : trim($value->first_name),
                                            'last_name' => (UtilString::is_empty_string($value->last_name)) ? "" : trim($value->last_name),
                                            'company_name' => (UtilString::is_empty_string($value->company_name)) ? "" : trim($value->company_name),
                                            'company_domain' => (UtilString::is_empty_string($company_domain)) ? "" : $company_domain,
                                            'industry' => (UtilString::is_empty_string($value->industry)) ? "" : trim($value->industry),
                                            'country' => (UtilString::is_empty_string($value->country)) ? "" : trim($value->country),
                                            'job_title' => (UtilString::is_empty_string($value->job_title)) ? "" : strip_tags($value->job_title),
                                            'status' => (UtilString::is_empty_string($value->status)) ? "" : trim($value->status)
                                        ];
                                        $new_insert ++;
                                    } else {
                                        $already_exist ++;
                                    }
                                } else {
                                    $invalid ++;
                                    $emails_not_load[] = $value;
                                }
                            } else {
                                $invalid ++;
                                $emails_not_load[] = $value;
                            }
                        }
                    }
                    if (!empty($insert)) {
                        $insert_chunk = array_chunk($insert, 100);
                        foreach ($insert_chunk AS $ic) {
                            $insertData = DB::table('email_data')->insert($ic);
                        }
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

    public function importEmailValidationImportView() {
        return view('emailvalidationimport');
    }

    public function importEmailForValidation(Request $request) {
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('upload_max_filesize', -1);
        ini_set('post_max_size ', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
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
                    $new_insert = 0;
                    $already_exist = 0;
                    $invalid = 0;
                    $already_exist_in_sheet = 0;
                    $duplicate = array();
                    foreach ($data as $key => $value) {
                        if (UtilString::is_email($value->email)) {
                            $email = trim($value->email);
                            if (!in_array($email, $duplicate)) {
                                $email_exist = EmailForValidation::where('email', $email)->count();
                                if ($email_exist == 0) {
                                    $insert[] = [
                                        'user_id' => Auth::id(),
                                        'email' => $email,
                                    ];
                                    $new_insert ++;
                                } else {
                                    $already_exist ++;
                                }
                            } else {
                                $duplicate[] = $email;
                                $already_exist_in_sheet ++;
                            }
                        } else {
                            $invalid ++;
                        }
                    }
                    if (!empty($insert)) {
                        $insert_chunk = array_chunk($insert, 100);
                        foreach ($insert_chunk AS $ic) {
                            $insertData = DB::table('email_for_validation')->insert($ic);
                        }
                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
                        } else {
                            Session::flash('error', 'Error inserting the data..');
                            return back();
                        }
                    }
                    $stats_data = array(
                        "already_exist" => $already_exist,
                        "duplicate_in_sheet" => $already_exist_in_sheet,
                        "inserted" => $new_insert,
                        "invalid_email" => $invalid
                    );
                    Session::flash('stats_data', $stats_data);
                    return back();
                }
            }
        }
    }

    public function exportContactData(Request $request) {
        if ($request->has('data')) {
            $data = $request->data;
            $data_array[] = array("Full Name", "First Name", "Last Name", "Title", "Company", "Location", "Experience", "Profile Link", "Company Url");
            foreach ($data AS $d) {
                $d = json_decode($d, true);
                $data_array[] = array(
                    "Full Name" => (isset($d['full_name'])) ? $d['full_name'] : "",
                    "First Name" => "",
                    "Last Name" => "",
                    "Title" => (isset($d['title'])) ? $d['title'] : "",
                    "Company" => (isset($d['company'])) ? $d['company'] : "",
                    "Location" => (isset($d['location'])) ? $d['location'] : "",
                    "Experience" => (isset($d['experience'])) ? $d['experience'] : "",
                    "Profile Link" => (isset($d['profile_link'])) ? $d['profile_link'] : "",
                    "Company Url" => (isset($d['company_url'])) ? $d['company_url'] : ""
                );
            }
            $type = "csv";
            return Excel::create('junkdata', function($excel) use ($data_array) {
                        $excel->sheet('mySheet', function($sheet) use ($data_array) {
                            $sheet->fromArray($data_array, null, 'A1', false, false);
                        });
                    })->download($type);
        } else {
            echo "No data Found For Export";
        }
    }

    public function importBounceEmailData(Request $request) {
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
                $data = array();
                $data = Excel::load($path, function($reader) {})->get();
                if (!empty($data) && $data->count()) {
                    $total = $data->count();
                    $duplicate_in_sheet = 0;
                    $already_exist_in_db = 0;
                    $inserted = 0;
                    $invalid = 0;
                    $duplicate = array();
                    $insert = array();
                    foreach ($data as $key => $value) {
                        if (isset($value->email)) {
                            $email = trim($value->email);
                            if (in_array($email, $duplicate)) {
                                $duplicate_in_sheet ++;
                            } else {
                                $duplicate[] = $email;
                                if (UtilString::is_email($email)) {
                                    $email_exist = BounceEmail::where('email', $email)->count();
                                    if ($email_exist == 0) {
                                        $insert_array = [
                                            'email' => $email
                                        ];
                                        $insert[] = $insert_array;
                                        $inserted ++;
                                    } else {
                                        $already_exist_in_db ++;
                                    }
                                } else {
                                    $invalid ++;
                                }
                            }
                        } else {
                            Session::flash('fail', 'sheet does not contain email filed');
                            return back();
                        }
                    }
                    if (!empty($insert)) {
                        $insert_chunk = array_chunk($insert, 100);
                        foreach ($insert_chunk AS $ic) {
                            $insertData = DB::table('bounce_email')->insert($ic);
                        }
                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
                        } else {
                            Session::flash('error', 'Error inserting the data..');
                            return back();
                        }
                    }
                }
                $stats_data = array(
                    "total" => $total,
                    "duplicate_in_sheet" => $duplicate_in_sheet,
                    "already_exist_in_db" => $already_exist_in_db,
                    "inserted" => $inserted,
                    "invalid" => $invalid
                );
                Session::flash('stats_data', $stats_data);
                return back();
            } else {
                Session::flash('error', 'File is a ' . $extension . ' file.!! Please upload a valid xls file..!!');
                return back();
            }
        }
    }
}
