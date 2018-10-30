<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DgContacts;
use App\DgUrl;
use App\Helpers\UtilDebug;
use DB;
class DgContactScrapper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrapper:dgcontacts';

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
        
        $scrapper_base_url  = "https://discoverorg.com";
        $limit = 1;
        $url = DgUrl::where('status','not processed')->where('category','People')->take($limit)->get();
        if($url->count() > 0){
            $url_data = $url->first();
            $id = $url_data->id;
            $url_param = $url_data->url_param;
            $pagination = $url_data->pagination;
            $processed = FALSE;
            for($i=1; $i <= $pagination; $i++){
                if($i == 1){
                    $list_url = $scrapper_base_url."/directory/person/list/$url_param";
                }else{
                    $list_url = $scrapper_base_url."/directory/person/list/$url_param?p=$i";
                }
                $html = new \Htmldom($list_url);
                $data = $html->find('div.container-fluid > div.mt-3 > div.directory-row');
                $result = array();
                foreach($data AS $d){
                    $first_child = $d->find('div.pr-2 > div.directory-entry');
                    $second_child = $d->find('div.pl-2 > div.directory-entry');
                    foreach($first_child AS $fc){
                        $a = $fc->find('a');
                        $span = $fc->find('span');
                        $a = $a[0];
                        $span = $span[0];
                        $link =  $scrapper_base_url.$a->href;
                        $name =  htmlspecialchars_decode(trim($a->plaintext,"'"));
                        $job_title = htmlspecialchars_decode($span->plaintext);
                        $html_view_source = new \Htmldom($link);
                        $html_view_source = $html_view_source->find('div.site-container');
                        $view_source = $html_view_source[0];
                        $result[] = ["name" => $name, "job_title" => $job_title,"link"=>$link,'view_source'=>$view_source];
                    }
                    
                    foreach($second_child AS $sc){
                        $a = $sc->find('a');
                        $span = $sc->find('span');
                        $a = $a[0];
                        $span = $span[0];
                        $link =  $scrapper_base_url.$a->href;
                        $name =  htmlspecialchars_decode(trim($a->plaintext,"'"));
                        $job_title = htmlspecialchars_decode($span->plaintext);
                        $html_view_source = new \Htmldom($link);
                        $html_view_source = $html_view_source->find('div.site-container');
                        $view_source = $html_view_source[0];
                        $result[] = ["name" => $name, "job_title" => $job_title,"link"=>$link,'view_source'=>$view_source];
                    }
                }
                if(count($result) > 0){
                    try {
                        $insertData = DB::table('dg_contacts')->insert($result);
                        $processed = TRUE;
                    } catch (\Illuminate\Database\QueryException $ex) {
                        echo $ex->getMessage();                  
                    }
                }
            }
            if($processed){
                DgUrl::where('id',$id)->update(['status'=>'processed']);
            }
        }else{
            UtilDebug::print_r_array("response", "No, Data For Processing");
        }
        UtilDebug::debug("End Processing");
    }
}
