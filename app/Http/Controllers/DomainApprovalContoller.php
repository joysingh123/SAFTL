<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChangedCompanies;
use App\CompaniesWithDomain;
use App\AvailableEmail;
use App\MatchedContact;
use App\Contacts;
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
                $updated_record = ChangedCompanies::where('id',$id)->where('status','not processed')->get();
                if($old_record->count() > 0 && $updated_record->count() > 0){
                    $old_domain = $old_record->first()->company_domain;
                    $linkedin_id = $old_record->first()->linkedin_id;
                    $updated_domain = $updated_record->first()->company_domain;
                    if($old_domain != $updated_domain){
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
                                MatchedContact::where('linkedin_id',$linkedin_id)->update(["email_status"=>NULL,"domain"=>$updated_domain,"email_format_available"=>"no"]);
                            }
                        }
                    }
                    if($old_record->first()->linkedin_url != $updated_record->first()->linkedin_url){
                        $old_record->first()->linkedin_url = $updated_record->first()->linkedin_url;
                        $old_record->first()->save();
                    }
                    
                    if($old_record->first()->company_name != $updated_record->first()->company_name){
                        $old_record->first()->company_name = $updated_record->first()->company_name;
                        $save_as = $old_record->first()->save();
                        if($save_as){
                            Contacts::where('linkedin_id',$linkedin_id)->update(['company_name'=>$updated_record->first()->company_name]);
                            MatchedContact::where('linkedin_id',$linkedin_id)->update(['company_name'=>$updated_record->first()->company_name]);
                        }
                    }
                    
                    if($old_record->first()->company_type != $updated_record->first()->company_type){
                        $old_record->first()->company_type = $updated_record->first()->company_type;
                        $save_as = $old_record->first()->save();
                    }
                    
                    if($old_record->first()->state != $updated_record->first()->state){
                        $old_record->first()->state = $updated_record->first()->state;
                        $save_as = $old_record->first()->save();
                    }
                    
                    if($old_record->first()->region != $updated_record->first()->region){
                        $old_record->first()->region = $updated_record->first()->region;
                        $save_as = $old_record->first()->save();
                    }
                    
                    if($old_record->first()->logo_url != $updated_record->first()->logo_url){
                        $old_record->first()->logo_url = $updated_record->first()->logo_url;
                        $save_as = $old_record->first()->save();
                    }
                    
                    if($old_record->first()->facebook_url != $updated_record->first()->facebook_url){
                        $old_record->first()->facebook_url = $updated_record->first()->facebook_url;
                        $save_as = $old_record->first()->save();
                    }
                    
                    if($old_record->first()->twitter_url != $updated_record->first()->twitter_url){
                        $old_record->first()->twitter_url = $updated_record->first()->twitter_url;
                        $save_as = $old_record->first()->save();
                    }
                    
                    if($old_record->first()->zoominfo_url != $updated_record->first()->zoominfo_url){
                        $old_record->first()->zoominfo_url = $updated_record->first()->zoominfo_url;
                        $save_as = $old_record->first()->save();
                    }
                    
                    if($old_record->first()->employee_count_at_linkedin != $updated_record->first()->employee_count_at_linkedin){
                        $old_record->first()->employee_count_at_linkedin = $updated_record->first()->employee_count_at_linkedin;
                        $save_as = $old_record->first()->save();
                    }
                    
                    if($old_record->first()->industry != $updated_record->first()->industry){
                        $old_record->first()->industry = $updated_record->first()->industry;
                        $save_as = $old_record->first()->save();
                        if($save_as){
                           MatchedContact::where('linkedin_id',$linkedin_id)->update(['industry'=>$updated_record->first()->industry]);
                        }
                    }
                    
                    if($old_record->first()->city != $updated_record->first()->city){
                        $old_record->first()->city = $updated_record->first()->city;
                        $save_as = $old_record->first()->save();
                        if($save_as){
                           MatchedContact::where('linkedin_id',$linkedin_id)->update(['city'=>$updated_record->first()->city]);
                        }
                    }
                    
                    if($old_record->first()->postal_code != $updated_record->first()->postal_code){
                        $old_record->first()->postal_code = $updated_record->first()->postal_code;
                        $save_as = $old_record->first()->save();
                        if($save_as){
                           MatchedContact::where('linkedin_id',$linkedin_id)->update(['postal_code'=>$updated_record->first()->postal_code]);
                        }
                    }
                    
                    if($old_record->first()->employee_size != $updated_record->first()->employee_size){
                        $old_record->first()->employee_size = $updated_record->first()->employee_size;
                        $save_as = $old_record->first()->save();
                        if($save_as){
                           MatchedContact::where('linkedin_id',$linkedin_id)->update(['employee_size'=>$updated_record->first()->employee_size]);
                        }
                    }
                    
                    if($old_record->first()->country != $updated_record->first()->country){
                        $old_record->first()->country = $updated_record->first()->country;
                        $save_as = $old_record->first()->save();
                        if($save_as){
                           MatchedContact::where('linkedin_id',$linkedin_id)->update(['country'=>$updated_record->first()->country]);
                        }
                    }
                    ChangedCompanies::where('id',$id)->update(['status'=>'processed']);
                    CompaniesWithDomain::where('id',$id)->update(['locked'=>false]);
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
