<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;
use App\ScrapeUrls;
use App\ScrappedDomain;

class ScrapeDomainFromUrlHunter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrapedomain:hunter';

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
        $urls = ScrapeUrls::where('process_for_domain_scrapping',0)->take(1)->get();
        $base_url = "https://hunter.io";
        if($urls->count() > 0){
            $url_for_processing = $urls->first()->url;
            if($url_for_processing == "https://hunter.io/companies"){
                $html = new \Htmldom($url_for_processing);
                $data = $html->find('section > div.container > div.row',1);
                $data = $data->find('div.col-md-4 > a');
                if(count($data) > 0){
                    foreach($data AS $d){
                        $domain = trim($d->plaintext);
                        $url = $base_url.trim($d->href);
                        $domain_exist = ScrappedDomain::where('domain',$domain)->get();
                        if($domain_exist->count() <= 0){
                            $scrapped_domain = new ScrappedDomain();
                            $scrapped_domain->scrape_urls_id = $url_for_processing = $urls->first()->id;
                            $scrapped_domain->domain = $domain;
                            $scrapped_domain->hunter_url_for_email_format_scrape = $url;
                            $scrapped_domain->save();
                        }
                    }
                    $urls->first()->process_for_domain_scrapping = 1;
                    $urls->first()->save();
                }
            }else{
                UtilDebug::debug("Processing_url:==> $url_for_processing");
                $html = new \Htmldom($url_for_processing);
                $data = $html->find('section.company-list > div.container > ul > div.row > li > a');
                if(count($data) > 0){
                    foreach($data AS $d){
                        $domain = trim($d->plaintext);
                        $url = $base_url.trim($d->href);
                        $domain_exist = ScrappedDomain::where('domain',$domain)->get();
                        if($domain_exist->count() <= 0){
                            $scrapped_domain = new ScrappedDomain();
                            $scrapped_domain->scrape_urls_id = $url_for_processing = $urls->first()->id;
                            $scrapped_domain->domain = $domain;
                            $scrapped_domain->hunter_url_for_email_format_scrape = $url;
                            $scrapped_domain->save();
                        }
                        $urls->first()->process_for_domain_scrapping = 1;
                        $urls->first()->save();
                    }
                }
            }
        }
        UtilDebug::debug("End Processing");
    }
}
