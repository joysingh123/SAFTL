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
class ImportDataController extends Controller {

    public function importComapniesWithDomainView() {
        return view('addcompanieswithdomain');
    }

    public function importContactView() {
        return view('addcompaniescontact');
    }
    
    public function importComapniesWithDomainData(Request $request) {
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
                    $inserted = 0;
                    foreach ($data as $key => $value) {
                        $company_linkedin_profile = ($value->company_linkedin_profile != "") ? $value->company_linkedin_profile : "";
                        $company_domain = ($value->company_domain != "") ? $value->company_domain : "";
                        $company_name = ($value->company_name != "") ? $value->company_name : "";
                        $employee_count_at_linkedin = ($value->employee_count_at_linkedin != "") ? (int) $value->employee_count_at_linkedin : 0;
                        $industry = ($value->industry != "") ? $value->industry : "";
                        $city = ($value->city != "") ? $value->city : "";
                        $employee_size = ($value->employee_size != "") ? $value->employee_size : "";
                        $country = ($value->country != "") ? $value->country : "";
                        $company_linkedin_profile = UtilString::clean_string($company_linkedin_profile);
                        $company_domain = UtilString::clean_string($company_domain);
                        $company_name = UtilString::clean_string($company_name);
                        $industry = UtilString::clean_string($industry);
                        $city = UtilString::clean_string($city);
                        $employee_size = UtilString::clean_string($employee_size);
                        $country = UtilString::clean_string($country);
                        $contact_exist = CompaniesWithDomain::where('company_linkedin_profile', $company_linkedin_profile)->where('company_domain', $company_domain)->count();
                        if($contact_exist == 0){
                            $insert[] = [
                                'company_linkedin_profile' => $company_linkedin_profile,
                                'company_domain' => UtilString::get_domain_from_url($company_domain),
                                'company_name' => $company_name,
                                'employee_count_at_linkedin' => $employee_count_at_linkedin,
                                'industry' => $industry,
                                'city' => $city,
                                'employee_size' => $employee_size,
                                'country' => $country
                            ];
                            $inserted ++;
                        }else{
                            $duplicate ++;
                        }
                    }
                    if(!empty($insert)){
                        $insertData = DB::table('companies_with_domain')->insert($insert);
                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
                        }else {                        
                            Session::flash('error', 'Error inserting the data..');
                            return back();
                        }
                    }
                }
                $stats_data = array(
                    "duplicate" => $duplicate,
                    "inserted" => $inserted
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
        $this->validate($request, array(
            'file' => 'required'
        ));
        if ($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {
                $path = $request->file->getRealPath();
                $data = Excel::load($path, function($reader) {
                            
                        })->get();
                $data = $data->where('full_name', '!=', null)->where('company', '!=', null)->where('title', '!=', null);
                if (!empty($data) && $data->count()) {
                    $duplicate = 0;
                    $inserted = 0;
                    $campaign_id_not_exist = 0;
                    $invalid_name = 0;

                    foreach ($data as $key => $value) {
                        $company_id = UtilString::get_company_id_from_url($value->company_url);
                        $first_name = "";
                        $last_name = "";
                        $status = "invalid";
                        $explode_name = explode(" ", $value->full_name);
                        if (count($explode_name) == 1) {
                            $first_name = $explode_name[0];
                            $status = "valid";
                        } else if (count($explode_name) == 2) {
                            $first_name = $explode_name[0];
                            $last_name = $explode_name[1];
                            $status = "valid";
                        }else {
                            $invalid_name ++;
                        }
                        if ($company_id <= 0) {
                            $status = "invalid";
                            $campaign_id_not_exist ++;
                        }
                        $full_name = ($value->full_name != "") ? $value->full_name : "";
                        $job_title = ($value->title != "") ? $value->title : "";
                        $company_name = ($value->company != "") ? $value->company : "";
                        $contact_exist = Contacts::where('full_name', $full_name)->where('job_title', $job_title)->where('company_name', $company_name)->count();

                        if ($contact_exist == 0) {
                            $inserted ++;
                            $insert[] = [
                                'full_name' => $full_name,
                                'first_name' => $first_name,
                                'last_name' => $last_name,
                                'company_name' => $company_name,
                                'job_title' => $job_title,
                                'experience' => ($value->experience != "") ? $value->experience : "",
                                'location' => ($value->location != "") ? $value->location : "",
                                'profile_link' => ($value->profile_link != "") ? $value->profile_link : "",
                                'company_url' => ($company_id > 0) ? "https://www.linkedin.com/company/$company_id/" : "",
                                'status' => $status,
                            ];
                        } else {
                            $duplicate ++;
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
                        "invalid_name" => $invalid_name
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
}
