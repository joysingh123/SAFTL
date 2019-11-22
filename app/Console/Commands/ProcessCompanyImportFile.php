<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\CompanyImport;
use Excel;
use App\CompaniesWithDomain;
use App\CompaniesWithoutDomain;
use DB;
class ProcessCompanyImportFile extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Company Import Files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        UtilDebug::debug("Start Processing");
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $company_import = CompanyImport::where('status', 'Under Processing')->orderBy('created_at', 'DESC')->get();
        if ($company_import->count() > 0) {
            foreach ($company_import AS $ci) {
                $user_id = $ci->user_id;
                $upload_path = public_path() . "/upload/companyimport/" . $user_id . "/";
                $uploadfilepath = $upload_path . $ci->upload_name;
                if (file_exists($uploadfilepath)) {
                    $duplicate_in_sheet = 0;
                    $already_exist_in_db = 0;
                    $inserted = 0;
                    $domain_not_exist = 0;
                    $junk_count = 0;
                    $insert = array();
                    $duplicate = array();
                    $linkedinids_array = array();
                    $data = Excel::load($uploadfilepath, function($reader) {})->get();
                    foreach ($data as $key => $value) {
                        if (UtilString::is_empty_string($value->company_domain) && UtilString::is_empty_string($value->linkedin_id) && UtilString::is_empty_string($value->company_name)) {
                        } else {
                            if (in_array($value, $duplicate)) {
                                $duplicate_in_sheet ++;
                            } else {
                                $duplicate[] = $value;
                                if (!UtilString::contains($value, "\u")) {
                                    if ((isset($value->company_domain) && isset($value->linkedin_id)) && (UtilString::contains($value->company_domain, "."))) {
                                        $linkedin_id = ($value->linkedin_id != "") ? UtilString::get_company_id_from_url($value->linkedin_id) : 0;
                                        if($linkedin_id == 0){
                                            $linkedin_id = UtilString::get_company_id_from_url($value->linkedin_url);
                                        }
                                        $linkedin_url = ($value->linkedin_url != "") ? $value->linkedin_url : "";
                                        $company_domain = ($value->company_domain != "") ? $value->company_domain : "";
                                        $company_name = ($value->company_name != "") ? $value->company_name : "";
                                        $company_type = ($value->company_type != "") ? $value->company_type : "";
                                        $employee_count_at_linkedin = ($value->employee_count_at_linkedin != "") ? (int) $value->employee_count_at_linkedin : 0;
                                        $industry = ($value->industry != "") ? $value->industry : "";
                                        $city = ($value->city != "") ? $value->city : "";
                                        $postal_code = ($value->postal_code != "") ? trim($value->postal_code) : "";
                                        $employee_size = ($value->employee_size != "") ? trim($value->employee_size) : "";
                                        $country = ($value->country != "") ? trim($value->country) : "";
                                        $state = NULL;
                                        if (isset($value->state)) {
                                            $state = ($value->state != "") ? trim($value->state) : NULL;
                                        }
                                        $logo_url = ($value->logo != "") ? trim($value->logo) : NULL;
                                        $facebook_url = ($value->facebook_url != "") ? trim($value->facebook_url) : NULL;
                                        $twitter_url = ($value->twitter_url != "") ? trim($value->twitter_url) : NULL;
                                        $zoominfo_url = ($value->zoominfo_url != "") ? trim($value->zoominfo_url) : NULL;

                                        $linkedin_url = UtilString::clean_string($linkedin_url);
                                        $company_domain = UtilString::clean_string($company_domain);
                                        $company_domain = UtilString::get_domain_from_url($company_domain);
                                        $company_name = UtilString::clean_string($company_name);
                                        $industry = UtilString::clean_string($industry);
                                        $city = UtilString::clean_string($city);
                                        $employee_size = UtilString::clean_string($employee_size);
                                        $country = UtilString::clean_string($country);
                                        $contact_exist = CompaniesWithDomain::where('linkedin_id', $linkedin_id)->count();
                                        if ($contact_exist == 0) {
                                            if (!empty($linkedin_id)) {
                                                $insert_array = [
                                                    'user_id' => $user_id,
                                                    'linkedin_id' => $linkedin_id,
                                                    'linkedin_url' => $linkedin_url,
                                                    'company_domain' => $company_domain,
                                                    'company_name' => $company_name,
                                                    'company_type' => $company_type,
                                                    'employee_count_at_linkedin' => $employee_count_at_linkedin,
                                                    'industry' => $industry,
                                                    'city' => $city,
                                                    'postal_code' => $postal_code,
                                                    'employee_size' => $employee_size,
                                                    'country' => $country,
                                                    'state' => $state,
                                                    'logo_url' => $logo_url,
                                                    'facebook_url' => $facebook_url,
                                                    'twitter_url' => $twitter_url,
                                                    'zoominfo_url' => $zoominfo_url
                                                ];
                                                $insert[] = $insert_array;
                                                $inserted ++;
                                            }
                                        } else {
                                            $already_exist_in_db ++;
                                        }
                                    } else {
                                        $domain_not_exist ++;
                                        $company_without_domain = CompaniesWithoutDomain::where('company_name', $value->company_name)->get();
                                        if ($company_without_domain->count() <= 0) {
                                            $company_d = new CompaniesWithoutDomain();
                                            $linkedin_id = ($value->linkedin_id != "") ? UtilString::get_company_id_from_url($value->linkedin_id) : 0;
                                            $company_d->linkedin_id = $linkedin_id;
                                            $company_d->company_domain = $value->company_domain;
                                            $company_d->company_name = $value->company_name;
                                            $company_d->employee_count_at_linkedin = $value->employee_count_at_linkedin;
                                            $company_d->industry = $value->industry;
                                            $company_d->city = $value->city;
                                            $company_d->employee_size = $value->employee_size;
                                            $company_d->country = $value->country;
                                            $company_d->state = $value->state;
                                            $company_d->logo_url = $value->logo;
                                            $company_d->facebook_url = $value->facebook_url;
                                            $company_d->twitter_url = $value->twitter_url;
                                            $company_d->zoominfo_url = $value->zoominfo_url;
                                            $company_d->save();
                                        }
                                    }
                                } else {
                                    $junk_count ++;
                                }
                            }
                        }
                    }
                    if (!empty($insert)) {
                        $insert_chunk = array_chunk($insert, 100);
                        foreach ($insert_chunk AS $ic) {
                            $insertData = DB::table('companies_with_domain')->insert($ic);
                        }
                        if ($insertData) {
                            DB::statement("update contacts A inner join companies_with_domain B on A.linkedin_id = B.linkedin_id set A.process_for_contact_match = 'not processed' where A.process_for_contact_match = 'company not found'");
                            UtilDebug::print_message('success', 'Your Data has successfully imported');
                        } else {
                            UtilDebug::print_message('error', 'Error inserting the data..');
                        }
                    }
                    $ci->inserted_in_db = $ci->inserted_in_db + $inserted;
                    $ci->duplicate_in_sheet = $ci->duplicate_in_sheet + $duplicate_in_sheet;
                    $ci->already_exist_in_db = $ci->already_exist_in_db + $already_exist_in_db;
                    $ci->domain_not_exist = $ci->domain_not_exist + $domain_not_exist;
                    $ci->junk_count = $ci->junk_count + $junk_count;
                    $ci->status = 'Completed';
                    $ci->save();
                } else {
                    echo $uploadfilepath . " Not Exist";
                }
            }
        }
        UtilDebug::debug("End Processing");
    }

}
