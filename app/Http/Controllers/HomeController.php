<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contacts;
use App\CompaniesWithDomain;
use App\CompaniesWithoutDomain;
use App\AvailableEmail;
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
        
        
        
        
        $data['companies_data'] = $companies_data;
        $data['cwd_data'] = $cwd_data;
        $data['contacts_stats'] = $contacts_data;
        $data['available_email'] = $available_email;
        return view('home')->with('data',$data);
    }
}
