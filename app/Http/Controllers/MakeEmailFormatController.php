<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GenerateEmailFormatTraits;
use App\Helpers\UtilDebug;

class MakeEmailFormatController extends Controller {

    use GenerateEmailFormatTraits;
    
    public function index() {
        $response = $this->generateEmailFormat();
        UtilDebug::print_r_array("stats", $response);
    }
}
