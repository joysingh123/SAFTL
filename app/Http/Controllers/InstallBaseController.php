<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InstallBase;
use Session;
use File;
use Excel;
use DB;
use App\Helpers\UtilString;
use App\Helpers\UtilConstant;
class InstallBaseController extends Controller
{
    public function installBaseView(){
        $install_base = InstallBase::where('status','Active')->get();
        return view('installbaseview')->with('install_base',$install_base);
    }
    
    public function importInstallBase(Request $request){
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('upload_max_filesize', -1);
        ini_set('post_max_size ', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        
        $this->validate($request, array(
            'file' => 'required',
            'install_base' => 'required'
        ));
        
        if ($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls") {
                $path = $request->file->getRealPath();
                $data = array();
                $install_base = $request->get('install_base');
                $data = Excel::load($path, function($reader) { })->get();
                $header = $data->getHeading();
                if (in_array('domain', $header)) {
                    if (!empty($data) && $data->count()) {
                        $duplicate_in_sheet = 0;
                        $already_exist_in_db = 0;
                        $inserted = 0;
                        $duplicate = array();
                        foreach ($data as $key => $value) {
                            if (UtilString::is_empty_string($value->domain)) {
                                
                            } else {
                                if (in_array($value, $duplicate)) {
                                    $duplicate_in_sheet ++;
                                } else {
                                    $duplicate[] = $value->domain;
                                    echo $value->domain;
                                }
                            }
                        }
                        if (!empty($insert)) {
                            $insert_chunk = array_chunk($insert, 100);
                            foreach ($insert_chunk AS $ic) {
                                $insertData = DB::table('companies_with_domain')->insert($ic);
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
                        "already_exist_in_db" => $already_exist_in_db,
                        "domain_not_exist" => $domain_not_exist,
                        "junk_count" => $junk_count,
                        "domain_not_found" => $domain_not_found,
                        "junk_data_array" => $junk_data_array
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
