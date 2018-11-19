<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Webhook;

class WebHookController extends Controller
{
    public function handle(Request $request){
        $data = json_decode($request->getContent(), true);
        if(count($data) > 0){
            foreach ($data AS $d){
                $email = $d->email;
                $event = $d->event;
                $reason = (isset($d->reason)) ? $d->reason : NULL;
                $webhook = new Webhook();
                $webhook->email = $email;
                $webhook->email_status = $event;
                $webhook->reason = $reason;
                $webhook->response = $d;
                $webhook->save();
            }
        }
    }
}
