<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\McaData;
class Populate99CorporateDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:99corporatesdomain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        UtilDebug::debug("Start Processing");
        ini_set('max_execution_time', -1);
        ini_set('memory_limit', -1);
        ini_set('mysql.connect_timeout', 600);
        ini_set('default_socket_timeout', 600);
        $limit = 1500;
//        $mca_data = McaData::where('status','not processed')->where('CORPORATE_IDENTIFICATION_NUMBER','L65110GJ1993PLC020769')->take($limit)->get();
        $mca_data = McaData::where('status','not processed')->take($limit)->get();
        $corporate_url = "https://www.99corporates.com/Company-Overview/";
        if($mca_data->count() > 0){
            foreach($mca_data AS $md){
                $cin = $md->CORPORATE_IDENTIFICATION_NUMBER;
                $c_name = $md->COMPANY_NAME;
                if(!UtilString::is_empty_string($c_name) && !UtilString::is_empty_string($cin)){
                    $company_name_for_url = str_replace("&", "", trim($c_name,"."));
                    $company_name_for_url = str_replace(" ", "-", trim($company_name_for_url,"."));
                    $scrape_url = $corporate_url.$company_name_for_url."/CIN/".$cin;
                    echo $scrape_url ."<br> ";
                    $html = new \Htmldom($scrape_url);
                    $website_url = $html->find('a[id=Body_web1]');
                    $facebok_url = $html->find('a[id=Body_FacebookLnk]');
                    $twitter_url = $html->find('a[id=Body_TwiterLnk]');
                    $linkedin_url = $html->find('a[id=Body_LinkedinLnk]');
                    if(isset($facebok_url[0])){
                        $facebok_url = $facebok_url[0]->href;
                    }else{
                        $facebok_url = "";
                    }
                    if(isset($twitter_url[0])){
                        $twitter_url = $twitter_url[0]->href;
                    }else{
                        $twitter_url = "";
                    }
                    if(isset($linkedin_url[0])){
                        $linkedin_url = $linkedin_url[0]->href;
                    }else{
                        $linkedin_url = "";
                    }
                    if(isset($website_url[0])){
                        $website_url = $website_url[0]->href;
                    }else{
                        $website_url = "";
                    }
                    $domain = UtilString::get_domain_from_url($website_url);
                    $domain = (!UtilString::is_empty_string($domain)) ? $domain : NULL;
                    $facebok_url = (!UtilString::is_empty_string($facebok_url)) ? $facebok_url : NULL;
                    $twitter_url = (!UtilString::is_empty_string($twitter_url)) ? $twitter_url : NULL;
                    $linkedin_url = (!UtilString::is_empty_string($linkedin_url)) ? $linkedin_url : NULL;
                    McaData::where('CORPORATE_IDENTIFICATION_NUMBER',$cin)->update(['COMPANY_DOMAIN'=>$domain,'FACEBOOK_LINK'=>$facebok_url,'TWITTER_LINK'=>$twitter_url,'LINKEDIN_LINK'=>$linkedin_url,'STATUS'=>'processed']);
                }else{
                    McaData::where('CORPORATE_IDENTIFICATION_NUMBER',$cin)->update(['COMPANY_DOMAIN'=>$domain,'STATUS'=>'processed']);
                }
            }
        }
        UtilDebug::debug("End Processing");
    }
}
