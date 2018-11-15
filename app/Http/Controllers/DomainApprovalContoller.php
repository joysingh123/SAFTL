<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChangedCompanies;
use App\CompaniesWithDomain;
use App\AvailableEmail;
use App\MatchedContact;
use Illuminate\Support\Facades\Auth;
use Session;
class DomainApprovalContoller extends Controller
{
    public function approveDomainView(Request $request){
        $companies_data = ChangedCompanies::all();
        return view('approvedomain')->with('companies',$companies_data);
    }
    public function approveDomain(Request $request){
        $id = $request->id;
        $message = "";
        if ($request->id > 0) {
            $user_id = Auth::id();
            $update = ChangedCompanies::where('id',$id)->update(['approve'=>true,"approved_by"=>$user_id]);
            if($update){
                $old_record = CompaniesWithDomain::where('id',$id)->get();
                $updated_record = ChangedCompanies::where('id',$id)->get();
                if($old_record->count() > 0 && $updated_record->count() > 0){
                    $old_domain = $old_record->first()->company_domain;
                    $updated_domain = $updated_record->first()->company_domain;
                    if($old_domain != $updated_record){
                        $update_first_name = $updated_record->first()->first_name;
                        $update_last_name = $updated_record->first()->last_name;
                        $update_email = $updated_record->first()->email;
                        $updated_user_id = $updated_record->first()->user_id;
                        $updated_company_name = $updated_record->first()->company_name;
                        $avilable_email = new AvailableEmail();
                        $avilable_email->user_id = $updated_user_id;
                        $avilable_email->email = $update_email;
                        $avilable_email->company_name = $updated_company_name;
                        $avilable_email->company_domain = $updated_domain;
                        $avilable_email->email = $update_email;
                        $avilable_email->first_name = $update_first_name;
                        $avilable_email->last_name = $update_last_name;
                        $avilable_email->country = "";
                        $avilable_email->job_title = "";
                        $save_as = $avilable_email->save();
                        if($save_as){
                            $old_record->first()->company_domain = $updated_domain;
                            $save_doamin = $old_record->first()->save();
                            if($save_doamin){
                                MatchedContact::where('linkedin_id',$old_record->first()->linkedin_id)->update(["email_status"=>NULL,"domain"=>$updated_domain,"email_format_available"=>"no"]);
                            }
                        }
                    }
                }else{
                   $message = "No, Record Found For Updation";
                   Session::flash('fail', $message);
                }
                $message = "Record Updated Successfully";
                Session::flash('success', $message);
            }else{
                $message = "Something went wrong";
                Session::flash('fail', $message);
            }
        }else{
            $message = "Invalid Request";
            Session::flash('fail', $message);
        }
        return $message;
    }
}
