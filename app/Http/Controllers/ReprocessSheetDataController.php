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
            $linkedin_ids = $sheet_data->pluck('Company_Linkedin_ID');
            print_r($linkedin_ids);
//            $linkedin_ids_chunk = array_chunk($linkedin_ids, 1000);
//            foreach($linkedin_ids_chunk AS $lic){
//                $companies = CompaniesWithDomain::whereIn('linkedin_id',$lic)->get();
//                if($companies->count() > 0){
//                    foreach ($companies AS $sd){
//                        $linkedin_id = $sd->linkedin_id;
//                        $company_domain = $sd->company_domain;
//                        $updated = MasterUserContact::where('Company_Linkedin_ID',$linkedin_id)->where('Email_Status','domain not found')->update(['Company_Domain'=>$company_domain,'Email_Status'=>'domain found']);
//                        if($updated){
//                            $response['record update'] += $updated;
//                        }
//                    }
//                }
//            }
//            $sheet_data = MasterUserContact::where('Email_Status','domain found')->distinct()->get(['Sheet_ID']);
//            if($sheet_data->count() > 0){
//                $plucked_ids = $sheet_data->pluck('Sheet_ID');
//                MasterUserSheet::whereIn('ID', $plucked_ids)->update(['Status' => 'Under Processing']);
//            }
        }
        print_r($response);
        UtilDebug::debug("End processing");
    }
}
