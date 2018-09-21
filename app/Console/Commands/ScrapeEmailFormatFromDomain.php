<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\ScrappedDomain;
use App\EmailFormat;
class ScrapeEmailFormatFromDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrapeemailformat:hunter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find Email Format From Hunter';

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
        $scrapper_domain = ScrappedDomain::where('email_format_scrape',0)->take(2)->get();
        if($scrapper_domain->count() > 0){
            $url_for_processing = $scrapper_domain->first()->hunter_url_for_email_format_scrape;
            $html = new \Htmldom($url_for_processing);
            $name = $html->find('div.search-container > div.company-intro-container > h1');
            $name = trim(strip_tags($name[0]));
            $email_format = $html->find('div.search-pattern > strong');
            $email_format = trim(strip_tags($email_format[0]));
            $total_email_found = $html->find('div.search-pattern > div.search-number');
            $total_email_found = trim(strip_tags($total_email_found[0]));
            $total_email_found = (int) filter_var($total_email_found, FILTER_SANITIZE_NUMBER_INT);
            
            echo "$name ==> $email_format ==> $total_email_found";
        }
//        echo $scrapper_domain;
        UtilDebug::debug("End Processing");
    }
}
