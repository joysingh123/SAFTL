<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Webhook;

class WebHookController extends Controller
{
    public function handle(Request $request){
        $webhook = new Webhook();
        $webhook->response = $request;
        $webhook->save();
    }
}
