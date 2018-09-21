<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\CronJobs;
use App\Helpers\UtilConstant;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */

    protected $commands = [
        'App\Console\Commands\MatchedContacts',
        'App\Console\Commands\GenerateEmailFormat',
        'App\Console\Commands\CreateEmail',
        'App\Console\Commands\ValidateEmail',
        'App\Console\Commands\ScrapeUrlForDomainScrappingFromHunter',
        'App\Console\Commands\ScrapeDomainFromUrlHunter'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
        // matched contacts
        $schedule->command('create:matchedcontact')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::MATCHED_CRON_JOB_NAME)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::MATCHED_CRON_JOB_NAME)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::MATCHED_CRON_JOB_NAME)->get();
            return ($cronjobs->first()->is_run = 'yes' && $cronjobs->first()->current_status = "Not Running");
        });
        
        //formate creation cron
       $schedule->command('generate:emailformat')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::MATCHED_CRON_EMAIL_FORMAT)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::MATCHED_CRON_EMAIL_FORMAT)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::MATCHED_CRON_EMAIL_FORMAT)->get();
            return ($cronjobs->first()->is_run = 'yes' && $cronjobs->first()->current_status = "Not Running");
        });
        
        //email creation
        
        $schedule->command('create:email')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::MATCHED_CRON_EMAIL_CREATE)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::MATCHED_CRON_EMAIL_CREATE)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::MATCHED_CRON_EMAIL_CREATE)->get();
            return ($cronjobs->first()->is_run = 'yes' && $cronjobs->first()->current_status = "Not Running");
        });
        
        //email validation
        
        $schedule->command('validate:email')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION)->get();
            return ($cronjobs->first()->is_run = 'yes' && $cronjobs->first()->current_status = "Not Running");
        });
        
        //Hunter url scrapper
        
        $schedule->command('scrapeurl:hunter')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_URL_HUNTER)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_URL_HUNTER)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_URL_HUNTER)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        //Hunter domain scrapper
        
        $schedule->command('scrapedomain:hunter')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_DOMAIN_HUNTER)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_DOMAIN_HUNTER)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_DOMAIN_HUNTER)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        //Hunter domain scrapper
        
//        $schedule->command('scrapeemailformat:hunter')->everyFiveMinutes()->withoutOverlapping()->before(function () {
//            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_DOMAIN_EMAIL_FORMAT)->get();
//            $cronjobs->first()->current_status = "Running";
//            $cronjobs->first()->save();
//        })->after(function () {
//            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_DOMAIN_EMAIL_FORMAT)->get();
//            $cronjobs->first()->current_status = "Not Running";
//            $cronjobs->first()->save();
//        })->when(function(){
//            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_DOMAIN_EMAIL_FORMAT)->get();
//            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
//                return true;
//            }
//            return false;
//        });
        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
