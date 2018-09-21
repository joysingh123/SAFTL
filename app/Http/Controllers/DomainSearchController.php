<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\UtilDebug;
use Ixudra\Curl\Facades\Curl;
class DomainSearchController extends Controller
{
    public function index(){
        $hunter_api_key = "82f775634e0084ebe309b995378908a29e7182fb";
        $domain_array = array('adib.eg','ahliunited.com','alahli.com','Alawwalbank.com','albaraka.com/','alinma.com','alkhaliji.com','alrayan.com','amenbank.com.tn','anb.com','attijariwafabank.com','baj.com.sa','bankalbilad.com','bankaudi.com.eg','bankfab.ae','bankmed.com.lb','bankofgeorgia.ge','bankofsharjah.com','banque-habitat.com.lb','barwabank.com','basisbank.ge','bbkonline.com','blombank.com','bmcebank.ma','bog.ge','bt.com.tn','burgan.com.tr','byblosbank.com','cab.jo','cbd.ae','cbk.com','cbq.qa','creditlibanais.com','dohabank.com.qa','e-gulfbank.com','emiratesnbd.com','emp-group.com','fransabank.com','gbp.ma','gib.com','hbtf.com','hbtf.com.jo','jkbank.com.jo','libertybank.ge','mashreqbank.com','nbk.com','qib.com.qa','qnb.com','rakbank.ae','riyadbank.com','sabb.com','saib.com','samba.com','sgmaroc.com','tbcbank.ge','unb-egypt.com','vtb.com.ua');
//        $domain_array = array('adib.eg');
        foreach($domain_array AS $domain){
            $doamin = trim($domain,"/");
            $hunter_url = "https://api.hunter.io/v2/domain-search?domain=$doamin&api_key=$hunter_api_key";
            $response = Curl::to($hunter_url)->get();
            $response_array = json_decode($response, true);
            $data = array("doamin"=>$response_array['data']['domain'],"pattern"=>$response_array['data']['pattern'],"organization"=>$response_array['data']['organization']);
            UtilDebug::print_r_array("<b>$doamin</b>", $data);
        }
    }
}
