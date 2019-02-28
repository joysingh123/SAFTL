<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use File;
use Excel;
use App\Helpers\UtilString;
use App\CompanyGroup;
use App\CompanyGroupMapping;

class CompanyGroupController extends Controller
{
    public function companyGroupView(){
        $company_group = CompanyGroup::where('status','Active')->get();
        return view('companygroupview')->with('company_group',$company_group);
    }
    
    public function importCompanyGroup(Request $request){
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('upload_max_filesize', -1);
        ini_set('post_max_size ', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        
        $this->validate($request, array(
            'file' => 'required',
            'company_group' => 'required'
        ));
        if ($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {
                $path = $request->file->getRealPath();
                $data = array();
                $company_group_id = $request->get('company_group');
                $data = Excel::load($path, function($reader) { })->get();
                $header = $data->getHeading();
                if (in_array('domain', $header)) {
                    if (!empty($data) && $data->count()) {
                        $duplicate_in_sheet = 0;
                        $inserted = 0;
                        $duplicate = array();
                        $invalid_domain = 0;
                        $already_exist_in_db = 0;
                        $invalid_domain_array = array();
                        foreach ($data as $key => $value) {
                            $domain = trim($value->domain);
                            if (UtilString::is_empty_string($domain)) {
                                
                            } else {
                                if (in_array($domain, $duplicate)) {
                                    $duplicate_in_sheet ++;
                                } else {
                                    $duplicate[] = $domain;
                                    if(UtilString::contains($domain, ".")){
                                        $company_group_data = CompanyGroupMapping::where('domain',$domain)->where('company_group_id',$company_group_id)->get();
                                        if($company_group_data->count() > 0){
                                            $already_exist_in_db ++;
                                        }else{
                                            $inser_data = array();
                                            $inser_data['domain'] = $domain;
                                            $inser_data['company_group_id'] = $company_group_id;
                                            $insert[] = $inser_data;
                                            $inserted++;
                                        }
                                    }else{
                                        $invalid_domain ++;
                                        $invalid_domain_array[] = $domain;
                                    }
                                    
                                }
                            }
                        }
                        if (!empty($insert)) {
                            $insert_chunk = array_chunk($insert, 100);
                            foreach ($insert_chunk AS $ic) {
                                $insertData = CompanyGroupMapping::insert($ic);
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
                        "inserted" => $inserted,
                        "duplicate_in_sheet" => $duplicate_in_sheet,
                        "invalid_domain" => $invalid_domain,
                        "already_exist_in_db" => $already_exist_in_db,
                        "invalid_domain_array" => $invalid_domain_array,
                    );
                    Session::flash('stats_data', $stats_data);
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
}
