<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\UtilDebug;
use App\ScrapeUrls;

class ScrapeUrlForDomainScrappingFromHunter extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrapeurl:hunter';

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
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        UtilDebug::debug("Start Processing");
        $urls = ScrapeUrls::where('process_for_url_scrapping', 0)->take(1)->get();
        $base_url = "https://hunter.io";
        if ($urls->count() > 0) {
            foreach ($urls AS $url) {
                $url_for_processing = $url->url;
                if ($url_for_processing == "https://hunter.io/companies") {
                    $html = new \Htmldom($url_for_processing);
                    $data = $html->find('section > div.container > div.row > div.col-md-12 > a');
                    if (count($data) > 0) {
                        foreach ($data AS $d) {
                            $found_url = $base_url . trim($d->href);
                            $exist_url = ScrapeUrls::where('url', $found_url)->get();
                            if ($exist_url->count() <= 0) {
                                $scrape_url = new ScrapeUrls();
                                $scrape_url->url = $found_url;
                                $scrape_url->from_where = 'hunter';
                                $scrape_url->save();
                            }
                        }
                    }
                    $urls->first()->process_for_url_scrapping = 1;
                    $urls->first()->save();
                } else {
                    UtilDebug::debug("Process Processing : $url_for_processing");
                }
            }
        }
        UtilDebug::debug("End Processing");
    }

}
