<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contacts;
use App\CompaniesWithDomain;
use App\CompaniesWithoutDomain;
use App\MatchedContact;
class ContactCompanyMatchController extends Controller
{
    public function index(Request $request){
        $response = array();
        $contacts = Contacts::where('process_for_contact_match',0)->take(10)->get();
        if($contacts->count() > 0){
            foreach($contacts AS $contact){
                $comapanies = CompaniesWithDomain::where('company_linkedin_profile',$contact->company_url)->get();
                if($comapanies->count() > 0){
                    $company =  $comapanies->first();
                    $matched_contact_exist = MatchedContact::where('full_name',$contact->full_name)->where('company_linkedin_page',$company->company_linkedin_profile)->where('job_title',$contact->job_title)->where('company_name',$company->company_name)->count();
                    if($matched_contact_exist == 0){
                        $matched_contact = new MatchedContact();
                        $matched_contact->full_name = $contact->full_name;
                        $matched_contact->first_name = $contact->first_name;
                        $matched_contact->last_name = $contact->last_name;
                        $matched_contact->job_title = $contact->job_title;
                        $matched_contact->company_name = $company->company_name;
                        $matched_contact->experience = $contact->experience;
                        $matched_contact->location = $contact->location;
                        $matched_contact->profile_link = $contact->profile_link;
                        $matched_contact->industry = $company->industry;
                        $matched_contact->company_linkedin_page = $company->company_linkedin_profile;
                        $matched_contact->city = $company->city;
                        $matched_contact->domain = $company->company_domain;
                        $matched_contact->employee_size = $company->employee_size;
                        $save_as = $matched_contact->save();
                        if($save_as == 1){
                            $contact->process_for_contact_match = 1;
                            $contact->save();
                        }
                    }
                }else{
                    $company_without_domain = new CompaniesWithoutDomain();
                    $company_without_domain->company_linkedin_profile = $contact->company_url;
                    $company_without_domain->save();
                }
            }
        }else{
            
        }
    }
}
