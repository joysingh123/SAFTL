<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\CreateEmailTraits;
use App\Helpers\UtilDebug;

class CreateEmailController extends Controller {

    use CreateEmailTraits;
    public function index(Request $request) {
        UtilDebug::debug("start processing");
        $response = $this->createEmail();
        UtilDebug::debug("End processing");
    }
}
