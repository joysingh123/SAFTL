<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DgCompanies;
use App\DgUrl;
use App\Helpers\UtilDebug;
use DB;
class DgScrapper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrapper:dg';

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
        $url = DgUrl::where('status','not processed')->where('category','Businesses')->take($limit)->get();
        if($url->count() > 0){
            $url_data = $url->first();
            $id = $url->first()->id;
            $url_param = $url_data->url_param;
            $pagination = $url_data->pagination;
            $processed = FALSE;
            for($i=2;$i <= $pagination;$i++){
                $list_url = $scrapper_base_url."/directory/company/list/$url_param?p=$i";
                $html = new \Htmldom($list_url);
                $data = $html->find('div.container-fluid > div.contact-section > div.mt-3 > div.directory-row');
                $result = array();
                foreach($data AS $d){
                    $first_child = $d->find('div.pr-2 > div.directory-entry > a');
                    if(isset($first_child[0])){
                        $first_child_loc = $d->find('div.pr-2 > div.directory-entry > span');
                        $link =  $scrapper_base_url.$first_child[0]->href;
                        $name =  htmlspecialchars_decode($first_child[0]->plaintext);
                        $location =  $first_child_loc[0]->plaintext;
                        $html_view_source = new \Htmldom($link);
                        $html_view_source = $html_view_source->find('div.site-container');
                        $view_source = $html_view_source[0];
                        $result[] = ["Company_Name" => $name, "Company_Url" => $link,"Location"=>$location,'View_Source'=>$view_source];  
                    }
                    $second_child = $d->find('div.pl-2 > div.directory-entry > a');
                    if(isset($second_child[0])){
                        $second_child_loc = $d->find('div.pl-2 > div.directory-entry > span');
                        $link =  $scrapper_base_url.$second_child[0]->href;
                        $name =  htmlspecialchars_decode($second_child[0]->plaintext);
                        $location =  $second_child_loc[0]->plaintext;
                        $html_view_source = new \Htmldom($link);
                        $html_view_source = $html_view_source->find('div.site-container');
                        $view_source = $html_view_source[0];
                        $result[] = ["Company_Name" => $name, "Company_Url" => $link,"Location"=>$location,'View_Source'=>$view_source];  
                    }  
                }
                if(count($result) > 0){
                    try {
                        $insertData = DB::table('dg_companies')->insert($result);
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
