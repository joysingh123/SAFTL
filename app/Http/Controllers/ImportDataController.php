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
use App\CompanyImport;
class ImportDataController extends Controller {

    public function importComapniesWithDomainView() {
        $user_id = Auth::user()->id;
        $company_import = CompanyImport::where('user_id',$user_id)->orderBy('created_at','DESC')->get();
        return view('addcompanieswithdomain')->with('company_import',$company_import);
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
        $user_id = Auth::user()->id;
        $this->validate($request, array(
            'file' => 'required'
        ));
        if ($request->hasFile('file')) {
            $file_original_image = $request->file->getClientOriginalName();
            $extension = File::extension($request->file->getClientOriginalName());
            $extension = strtolower($extension);
            if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {
                $path = $request->file->getRealPath();
                $file_original_image_final = time().".$extension";
                $upload_path = public_path()."/upload/companyimport/$user_id/";
                $data = array();
                $data = Excel::load($path, function($reader) {})->get();
                $header = $data->getHeading();
                if (in_array('linkedin_id', $header) && in_array('linkedin_url', $header) && in_array('company_domain', $header) && in_array('company_name', $header) && in_array('company_type', $header) && in_array('industry', $header) && in_array('city', $header) && in_array('employee_size', $header)) {
                    if (!empty($data) && $data->count()) {
                        $request->file->move($upload_path,$file_original_image_final);
                        $company_import = new CompanyImport();
                        $company_import->user_id = $user_id;
                        $company_import->name = $file_original_image;
                        $company_import->upload_name = $file_original_image_final;
                        $company_import->total_contact = $data->count();
                        $company_import->inserted_in_db = 0;
                        $company_import->duplicate_in_sheet = 0;
                        $company_import->already_exist_in_db = 0;
                        $company_import->domain_not_exist = 0;
                        $company_import->junk_count = 0;
                        $company_import->status = 'Under Processing';
                        $company_import->save();
                        Session::flash('success', "File Uploaded Successfully");
                        return back();
                    }else{
                        Session::flash('error', "File Is Empty"); 
                        return back();
                    }
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
