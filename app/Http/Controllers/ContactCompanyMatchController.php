<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Traits\ContactCompanyMatchTraits;

class ContactCompanyMatchController extends Controller
{
    use ContactCompanyMatchTraits;
    
    
    public function index(){
        $response = $this->matchContactCompany();
        return view('contactcompanymatch')->with("response",$response);
    }
}
