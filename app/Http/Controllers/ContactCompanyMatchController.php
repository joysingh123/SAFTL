<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contacts;
use App\CompaniesWithDomain;
use App\CompaniesWithoutDomain;
use App\MatchedContact;
use App\Helpers\UtilDebug;
class ContactCompanyMatchController extends Controller
{
    public function index(Request $request){
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('upload_max_filesize', -1);
        ini_set('post_max_size ', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $response = array();
        $limit = 2000;
        $contacts = Contacts::where('process_for_contact_match','not processed')->take($limit)->get();
        if($contacts->count() > 0){
            $new_insert_in_match = 0;
            $already_exist_in_match = 0;
            $already_in_company_not_found = 0;
            $new_in_company_not_found = 0;
            foreach($contacts AS $contact){
//                UtilDebug::print_message("contact", $contact);
                $comapanies = CompaniesWithDomain::where('linkedin_id',$contact->linkedin_id)->get();
                if($comapanies->count() > 0){
                    $company =  $comapanies->first();
                    $matched_contact_exist = MatchedContact::where('full_name',$contact->full_name)->where('linkedin_id',$company->linkedin_id)->where('job_title',$contact->job_title)->where('company_name',$company->company_name)->count();
                    if($matched_contact_exist == 0){
                        $matched_contact = new MatchedContact();
                        $matched_contact->contact_id = $contact->id;
                        $matched_contact->linkedin_id = $contact->linkedin_id;
                        $matched_contact->full_name = $contact->full_name;
                        $matched_contact->first_name = $contact->first_name;
                        $matched_contact->last_name = $contact->last_name;
                        $matched_contact->job_title = $contact->job_title;
                        $matched_contact->company_name = $company->company_name;
                        $matched_contact->experience = $contact->experience;
                        $matched_contact->location = $contact->location;
                        $matched_contact->profile_link = $contact->profile_link;
                        $matched_contact->industry = $company->industry;
                        $matched_contact->country = $company->country;
                        $matched_contact->city = $company->city;
                        $matched_contact->postal_code = $company->postal_code;
                        $matched_contact->domain = $company->company_domain;
                        $matched_contact->employee_size = $company->employee_size;
                        $matched_contact->tag = $contact->tag;
                        $matched_contact->title_level = $contact->title_level;
                        $matched_contact->department = $contact->department;
                        $save_as = $matched_contact->save();
                        if($save_as == 1){
                            $new_insert_in_match ++;
                            $contact->process_for_contact_match = 'matched';
                            $contact->save();
                        }
                    }else{
                        $contact->process_for_contact_match = 'matched';
                        $contact->save();
                        $already_exist_in_match ++;
                    }
                }else{
                    $comapany_without_domain = CompaniesWithoutDomain::where('linkedin_id',$contact->linkedin_id)->where('company_name',$contact->company_name)->get();
                    if($comapany_without_domain->count() > 0){
                        $already_in_company_not_found ++;
                        $contact_count = $comapany_without_domain->first()->contacts_count;
                        $comapany_without_domain->first()->contacts_count = $comapany_without_domain->first()->contacts_count + 1;
                        $comapany_without_domain->first()->save();
                        $contact->process_for_contact_match = 'company not found';
                        $contact->save();
                    }else{
                        $comapany_without_domain = new CompaniesWithoutDomain();
                        $comapany_without_domain->linkedin_id = $contact->linkedin_id;
                        $comapany_without_domain->company_name = $contact->company_name;
                        $comapany_without_domain->contacts_count = 1;
                        if($comapany_without_domain->save() == 1){
                            $new_in_company_not_found ++;
                            $contact->process_for_contact_match = 'company not found';
                            $contact->save();
                        }
                    }
                }
            }
           $response['status'] = "Success";
           $response['message'] = "Processed Successfully"; 
           $response['stats'] = array(
               "Record Processing Limit"=>$limit,
               "Found Record For Processing"=>$contacts->count(),
               "New In Match"=>$new_insert_in_match,
               "Already In Match"=>$already_exist_in_match,
               "New In Domain Not Found"=>$new_in_company_not_found,
               "Already Exist In Domain Not Found"=>$already_in_company_not_found
                   );
        }else{
           $response['status'] = "Fail";
           $response['message'] = "No record found for processing";
        }
        return view('contactcompanymatch')->with("response",$response);
    }
}
