<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Webhook;

class WebHookController extends Controller
{
    public function handle(Request $request){
        $data =  $request->getContent();
        $webhook = new Webhook();
        $webhook->response = $data;
        $webhook->save();
    }
}
