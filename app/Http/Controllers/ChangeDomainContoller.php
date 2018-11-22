<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\CompaniesWithDomain;
use App\Helpers\UtilString;
use App\IndustryMaster;
use App\CountryMaster;
use App\EmployeeSizeMaster;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\ChangedCompanies;
use Session;

class ChangeDomainContoller extends Controller {

    public function changeDomainView() {
        $industry = DB::table('s_industry_master')->select('Industry')->get();
        $country = DB::table('s_country_master')->select('Country Name AS name')->get();
        $employeesize = DB::table('s_employee_size_master')->select('employee_size')->get();
        $plucked_industry = $industry->pluck('Industry');
        $plucked_country = $country->pluck('name');
        $plucked_employeesize = $employeesize->pluck('employee_size');
        $seed_data = array("industry"=>$plucked_industry,"country"=>$plucked_country,"employeesize"=>$plucked_employeesize);
        return view('changedomainview')->with("seed_data",$seed_data);
    }
    
    public function companyFilteredData(Request $request){
        $domain = trim($request->domain);
        $country = trim($request->country);
        $mx_record = trim($request->mx_record);
        $city = trim($request->city);
        $industry = trim($request->industry);
        $employee_size = trim($request->employee_size);
        $employee_count = trim($request->employee_count);
        $company_type = trim($request->company_type);
        if (UtilString::is_empty_string($domain) && UtilString::is_empty_string($country) && UtilString::is_empty_string($mx_record) && UtilString::is_empty_string($city) && UtilString::is_empty_string($industry) && UtilString::is_empty_string($employee_size) && UtilString::is_empty_string($employee_count) && UtilString::is_empty_string($company_type)) {
            $data = DB::table('companies_with_domain')->select('*')->where('company_domain', 'test');
        } else {
            $data = DB::table('companies_with_domain')->select('*')->where('locked', false);
            if (!UtilString::is_empty_string($domain)) {
                $data->where('company_domain', $domain);
            }
            if (!UtilString::is_empty_string($country)) {
                $data->where('country', $country);
            }
            if (!UtilString::is_empty_string($mx_record)) {
                $data->where('mx_record', $mx_record);
            }
            if (!UtilString::is_empty_string($city)) {
                $data->where('city', $city);
            }
            if (!UtilString::is_empty_string($industry)) {
                $data->where('industry', $industry);
            }
            if (!UtilString::is_empty_string($employee_size)) {
                $data->where('employee_size', $employee_size);
            }
            if (!UtilString::is_empty_string($employee_count)) {
                $data->where('employee_count_at_linkedin', $employee_count);
            }
            if (!UtilString::is_empty_string($employee_size)) {
                $data->where('employee_size', $employee_size);
            }
            if (!UtilString::is_empty_string($company_type)) {
                $data->where('company_type', $company_type);
            }
        }
        $data = $data->orderBy('total_record', 'desc')->paginate(100);
        return view('filteredcompanydata', compact('data'))->render();
    }
    
    public function editCompanyView(Request $request) {
        $response = array();
        $id = $request->id;
        $companies = CompaniesWithDomain::where('id', $id)->get();
        $country = CountryMaster::all(['ID', 'Country Name AS country_name']);
        $industry = IndustryMaster::all();
        $employee_size = EmployeeSizeMaster::all();
        $seed_data = array('country' => $country, 'industry' => $industry, 'employee_size' => $employee_size);
        return view('editcompany')->with('company', $companies->first())->with('seed_data', $seed_data);
    }

    public function editCompany(Request $request) {
        $rules = array(
            'company_domain' => 'required',
            'company_name' => 'required',
            'remark' => 'required',
            'employee_count' => 'integer',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        } else {
            if (UtilString::contains($request->company_domain, ".")) {
                $id = $request->id;
                $existing_companies = CompaniesWithDomain::where('id', $id)->get();
                $existing_companies = $existing_companies->first();
                $linkedin_id = trim($request->linkedin_id);
                $company_name = trim($request->company_name);
                $company_domain = trim(UtilString::get_domain_from_url($request->company_domain));
                $linkedin_url = trim($request->linkedin_url);
                $employee_count = trim($request->employee_count);
                $industry = trim($request->industry);
                $city = trim($request->city);
                $postal_code = trim($request->postal_code);
                $employee_size = trim($request->employee_size);
                $country = trim($request->country);
                $website = trim($request->website);
                $remark = trim($request->remark);
                $first_name = trim($request->first_name);
                $last_name = trim($request->last_name);
                $email = trim($request->email);
                $state = trim($request->state);
                $region = trim($request->region);
                $logo = trim($request->logo);
                $facebook_url = trim($request->facebook_url);
                $twitter_url = trim($request->twitter_url);
                $zoominfo_url = trim($request->zoominfo_url);
                
                $change_perform = false;
                $is_domain_change = false;
                if ($company_name != $existing_companies->company_name) {
                    $change_perform = true;
                }
                if ($company_domain != $existing_companies->company_domain) {
                    $change_perform = true;
                    $is_domain_change = true;
                }
                if ($linkedin_url != $existing_companies->linkedin_url) {
                    $change_perform = true;
                }
                if ($employee_count != $existing_companies->employee_count_at_linkedin) {
                    $change_perform = true;
                }
                if ($industry != $existing_companies->industry) {
                    $change_perform = true;
                }
                if ($city != $existing_companies->city) {
                    $change_perform = true;
                }
                if ($postal_code != $existing_companies->postal_code) {
                    $change_perform = true;
                }
                if ($employee_size != $existing_companies->employee_size) {
                    $change_perform = true;
                }
                if ($country != $existing_companies->country) {
                    $change_perform = true;
                }
                if ($website != $existing_companies->website) {
                    $change_perform = true;
                }
                if ($state != $existing_companies->state) {
                    $change_perform = true;
                }
                if ($logo != $existing_companies->logo_url) {
                    $change_perform = true;
                }
                if ($region != $existing_companies->region) {
                    $change_perform = true;
                }
                if ($facebook_url != $existing_companies->facebook_url) {
                    $change_perform = true;
                }
                if ($twitter_url != $existing_companies->twitter_url) {
                    $change_perform = true;
                }
                if ($zoominfo_url != $existing_companies->zoominfo_url) {
                    $change_perform = true;
                }
                if ($change_perform) {
                    $changed_companies = new ChangedCompanies();
                    $changed_companies->id = $id;
                    $changed_companies->user_id = Auth::id();
                    $changed_companies->linkedin_id = $linkedin_id;
                    $changed_companies->linkedin_url = $linkedin_url;
                    $changed_companies->company_domain = $company_domain;
                    $changed_companies->company_name = $company_name;
                    $changed_companies->employee_count_at_linkedin = $employee_count;
                    $changed_companies->industry = $industry;
                    $changed_companies->city = $city;
                    $changed_companies->postal_code = $postal_code;
                    $changed_companies->employee_size = $employee_size;
                    $changed_companies->country = $country;
                    $changed_companies->website = $website;
                    $changed_companies->remark = $remark;
                    $changed_companies->state = $state;
                    $changed_companies->region = $region;
                    $changed_companies->logo_url = $logo;
                    $changed_companies->facebook_url = $facebook_url;
                    $changed_companies->twitter_url = $twitter_url;
                    $changed_companies->zoominfo_url = $zoominfo_url;
                    
                    $save_as = false;
                    if ($is_domain_change) {
                        $rules = array(
                            'first_name' => 'required',
                            'last_name' => 'required',
                            'email' => 'required|email',
                        );
                        $validator = Validator::make($request->all(), $rules);
                        if ($validator->fails()) {
                            return Redirect::back()->withInput()->withErrors($validator);
                        } else {
                            $email_doamin =  substr($email,strpos($email,'@')+1,strlen($email)-1);
                            if($email_doamin != $company_domain){
                                Session::flash('error', "you have entered wrong domain email");
                                return back();
                            }else{
                                $changed_companies->first_name = $first_name;
                                $changed_companies->last_name = $last_name;
                                $changed_companies->email = $email;
                                $save_as = $changed_companies->save();
                            }
                        }
                    } else {
                        $save_as = $changed_companies->save();
                    }
                    if ($save_as) {
                        CompaniesWithDomain::where('id', $id)->update(['locked' => true]);
                        Session::flash('success', 'company info updated successfully');
                        return back();
                    }
                } else {
                    Session::flash('error', 'You did not change any thing. please make change first');
                    return back();
                }
            } else {
                Session::flash('error', 'You entered wrong domain');
                return back();
            }
        }
    }
}
