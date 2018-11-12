<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MatchedContact;
use App\Helpers\UtilString;
use DB;
use App\Companies;
class ExtractDataController extends Controller
{
    public function extractDataView(Request $request){
        return view('dataextraction');
    }
    
    public function extractDataForAutoComplate(Request $request){
        $column =  $request->data;
        $term = $request->get('term');
        $data = "";
        if($column == "country"){
            $filter_data = DB::table('matched_contacts')->select(DB::raw('DISTINCT country'))->where('country', 'LIKE', "%$term%")->orderBy('country')->get();
            $plucked = $filter_data->pluck('country');
            $filter_data = $plucked->all();
            $data = json_encode($filter_data);
        }else if($column == "city"){
            $filter_data = DB::table('matched_contacts')->select(DB::raw('DISTINCT city'))->where('city', 'LIKE', "%$term%")->orderBy('city')->get();
            $plucked = $filter_data->pluck('city');
            $filter_data = $plucked->all();
            $data = json_encode($filter_data);
        }else if($column == "industry"){
            $filter_data = DB::table('matched_contacts')->select(DB::raw('DISTINCT industry'))->where('industry', 'LIKE', "%$term%")->orderBy('industry')->get();
            $plucked = $filter_data->pluck('industry');
            $filter_data = $plucked->all();
            $data = json_encode($filter_data);
        }else if($column == "department"){
            $filter_data = DB::table('matched_contacts')->select(DB::raw('DISTINCT department'))->where('department', 'LIKE', "%$term%")->orderBy('department')->get();
            $plucked = $filter_data->pluck('department');
            $filter_data = $plucked->all();
            $data = json_encode($filter_data);
        }else if($column == "titlelevel"){
            $filter_data = DB::table('matched_contacts')->select(DB::raw('DISTINCT title_level'))->where('title_level', 'LIKE', "%$term%")->orderBy('title_level')->get();
            $plucked = $filter_data->pluck('title_level');
            $filter_data = $plucked->all();
            $data = json_encode($filter_data);
        }else if($column == "employeesize"){
            $filter_data = DB::table('matched_contacts')->select(DB::raw('DISTINCT employee_size'))->where('employee_size', 'LIKE', "%$term%")->orderBy('employee_size')->get();
            $plucked = $filter_data->pluck('employee_size');
            $filter_data = $plucked->all();
            $data = json_encode($filter_data);
        }else if($column == "tag"){
            $filter_data = DB::table('matched_contacts')->select(DB::raw('DISTINCT tag'))->where('tag', 'LIKE', "%$term%")->orderBy('tag')->get();
            $plucked = $filter_data->pluck('tag');
            $filter_data = $plucked->all();
            $data = json_encode($filter_data);
        }
        return $data;
    }
    public function extractData(Request $request){
        $response = array();
        $country =  ($request->has("country")) ? $request->country : "";
        $city =  ($request->has("city")) ? $request->city : "";
        $industry =  ($request->has("industry")) ? $request->industry : "";
        $department =  ($request->has("department")) ? $request->department : "";
        $title_level =  ($request->has("department")) ? $request->title_level : "";
        $employee_size =  ($request->has("department")) ? $request->employee_size : "";
        $only_email = ($request->has("only_email")) ? $request->only_email : "false";
        $valid_email = ($request->has("valid_email")) ? $request->valid_email : "false";
        $tag =  ($request->has("department")) ? $request->tag : "";
        if(($valid_email == "true" || $only_email == "true") || !UtilString::is_empty_string($tag) || !UtilString::is_empty_string($employee_size) || !UtilString::is_empty_string($title_level) || !UtilString::is_empty_string($country) || !UtilString::is_empty_string($city) || !UtilString::is_empty_string($industry) || !UtilString::is_empty_string($department)){
            $data = DB::table('matched_contacts')->
                    select('first_name','last_name','email','domain','email_validation_date','email_status');
            if(!UtilString::is_empty_string($country)){
                $data->where('country','LIKE',"%$country%"); 
            } 
            if(!UtilString::is_empty_string($city)){
                $data->where('city','LIKE',"%$city%"); 
            } 
            if(!UtilString::is_empty_string($industry)){
                $data->where('industry','LIKE',"%$industry%"); 
            } 
            if(!UtilString::is_empty_string($department)){
                $data->where('department','LIKE',"%$department%"); 
            } 
            if(!UtilString::is_empty_string($title_level)){
                $data->where('title_level','LIKE',"%$title_level%"); 
            } 
            if(!UtilString::is_empty_string($employee_size)){
                $data->where('employee_size','LIKE',"%$employee_size%"); 
            } 
            if(!UtilString::is_empty_string($tag)){
                $data->where('tag','LIKE',"%$tag%"); 
            }
           if($only_email == "true"){
                $data->where('email','!=',"null"); 
            }
            if($valid_email == "true"){
                $data->where('email_status','=',"valid");
            }
            $processed_data =  $data->get();
            $count = $processed_data->count();
            if($count <= 0){
                $response['status'] = "Fail";
                $response['message'] = "No, Result Found.";
            }else{
                $response['status'] = "Success";
                $response['data'] = $processed_data;
                $response['total'] = $count;
            }
        }else{
            $response['status'] = "Fail";
            $response['message'] = "No, data filter selected";
        }
        return response()->json($response);
    }
}
