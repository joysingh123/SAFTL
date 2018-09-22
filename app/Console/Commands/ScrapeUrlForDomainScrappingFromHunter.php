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
                    UtilDebug::debug("Process Processing : $url->id");
                    if ($url->id <= 59) {
                        $html = new \Htmldom($url_for_processing);
                        $data = $html->find('section > div.container > div.row > div.col-md-12 > p');
                        if (isset($data[1])) {
                            if ($data[1]->plaintext == "Pages:") {
                                $dom = $html->find('section > div.container > div.row > div.col-md-12 > p', 1)->nextSibling();
                                $pages = array();
                                while ($dom != null) {
                                    $page_num = trim($dom->plaintext);
                                    if (is_numeric($page_num)) {
                                        $pages[] = $page_num;
                                    }
                                    $dom = $dom->nextSibling();
                                }
                                if (count($pages) > 0) {
                                    for ($i = $pages[0]; $i <= $pages[count($pages) - 1]; $i++) {
                                        $db_url = $url_for_processing . "/$i";
                                        $exist_url = ScrapeUrls::where('url', $db_url)->get();
                                        if ($exist_url->count() <= 0) {
                                            $scrape_url = new ScrapeUrls();
                                            $scrape_url->url = $db_url;
                                            $scrape_url->from_where = 'hunter';
                                            $scrape_url->save();
                                        }
                                    }
                                    $url->process_for_url_scrapping = 1;
                                    $url->save();
                                }
                            }
                        } else {
                            $url->process_for_url_scrapping = 1;
                            $url->save();
                        }
                    } else {
                        $url->process_for_url_scrapping = 1;
                        $url->save();
                    }
                }
            }
        }
        UtilDebug::debug("End Processing");
    }

}
