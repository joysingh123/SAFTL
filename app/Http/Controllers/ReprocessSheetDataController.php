<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompaniesWithDomain;
use App\MasterUserContact;
use App\MasterUserSheet;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;

class ReprocessSheetDataController extends Controller
{
    public function index(){
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('post_max_size ', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        UtilDebug::debug("Start processing");
        $sheet_data = MasterUserContact::where('Email_Status','domain not found')->distinct()->get(['Company_Linkedin_ID']);
        $response = array();
        if($sheet_data->count() > 0){
            $response['total'] = $sheet_data->count();
            $response['record update'] = 0;
            foreach ($sheet_data AS $sd){
                $linkedin_id = $sd->Company_Linkedin_ID;
                $company_domain_data = CompaniesWithDomain::where('linkedin_id',$linkedin_id)->get();
                if($company_domain_data->count() > 0){
                    $company_domain = $company_domain_data->first()->company_domain;
                    $updated = MasterUserContact::where('Company_Linkedin_ID',$linkedin_id)->where('Email_Status','domain not found')->update(['Company_Domain'=>$company_domain,'Email_Status'=>'domain found']);
                    if($updated){
                        $response['record update'] += $updated;
                    }
                }
            }
            $sheet_data = MasterUserContact::where('Email_Status','domain found')->distinct()->get(['Sheet_ID']);
            if($sheet_data->count() > 0){
                $plucked_ids = $sheet_data->pluck('Sheet_ID');
                MasterUserSheet::whereIn('ID', $plucked_ids)->update(['Status' => 'Under Processing']);
            }
        }
        print_r($response);
        UtilDebug::debug("End processing");
    }
}
