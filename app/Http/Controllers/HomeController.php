<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contacts;
use App\CompaniesWithDomain;
use App\CompaniesWithoutDomain;
use App\AvailableEmail;
use App\MatchedContact;
use App\Emails;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        
        $data = array();
        
        //contacts
        $contacts_data = array();
        $contacts_data['total'] = Contacts::count();
        $contacts_data['processed'] = Contacts::where('process_for_contact_match','!=','not processed')->count();
        $contacts_data['not_processed'] = Contacts::where('process_for_contact_match','=','not processed')->count();
        
        //companies with domain
        $companies_data = array();
        $companies_data['total'] = CompaniesWithDomain::count();
        $companies_data['processed'] = 0;
        $companies_data['not_processed'] = 0;
        
        //companies without domain
        $cwd_data = array();
        $cwd_data['total'] = CompaniesWithoutDomain::count();
        $cwd_data['processed'] = 0;
        $cwd_data['not_processed'] = 0;
        
        // Available Email
        $available_email = array();
        $available_email['total'] = AvailableEmail::count();
        $available_email['processed'] = AvailableEmail::where('status',"!=","")->count();
        $available_email['not_processed'] = AvailableEmail::where('status',"=","")->count();
        
        $matched_data = array();
        $matched_data['total'] = MatchedContact::count();
        $matched_data['email_created'] = MatchedContact::where('email_status',"=","created")->count();
        $matched_data['not_processed'] = MatchedContact::whereNULL('email_status')->count();
        
//        $emails = Emails::groupBy('matched_contact_id')->get();
        
        $emails_data = array();
        $emails_data['total'] = Emails::count();
        $emails_data['unique_email'] = Emails::all()->groupBy('matched_contact_id')->count();
        
        $data['companies_data'] = $companies_data;
        $data['emails_data'] = $emails_data;
        $data['cwd_data'] = $cwd_data;
        $data['contacts_stats'] = $contacts_data;
        $data['available_email'] = $available_email;
        $data['matched_data'] = $matched_data;
        return view('home')->with('data',$data);
    }
}
