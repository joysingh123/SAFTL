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
        'App\Console\Commands\GenearteDefaultFormat',
        'App\Console\Commands\CreateEmail',
        'App\Console\Commands\RemoveEmail',
        'App\Console\Commands\ValidateEmail',
        'App\Console\Commands\ValidateEmailCron2',
        'App\Console\Commands\ValidateEmailCron3',
        'App\Console\Commands\ValidateEmailCron4',
        'App\Console\Commands\ValidateEmailCron5',
        'App\Console\Commands\ValidateEmailCron6',
        'App\Console\Commands\ValidateEmailCron7',
        'App\Console\Commands\ValidateEmailCron8',
        'App\Console\Commands\ValidateEmailCron9',
        'App\Console\Commands\ValidateEmailCron10',
        'App\Console\Commands\EmailFormatPercentage',
        'App\Console\Commands\RemoveApiValidEmails',
        'App\Console\Commands\UserImportEmailValidation',
        'App\Console\Commands\UserImportEmailValidation2',
        'App\Console\Commands\UserImportEmailValidation3',
        'App\Console\Commands\PopulateCompaniesWithDomain'
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
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
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
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
         //email format percentage
        
        $schedule->command('percentage:emailformat')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_CALCULATE_DOMAIN_EMAIL_FORMAT_PERCENTAGE)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_CALCULATE_DOMAIN_EMAIL_FORMAT_PERCENTAGE)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_CALCULATE_DOMAIN_EMAIL_FORMAT_PERCENTAGE)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
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
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        // email remove
        
        $schedule->command('remove:email')->everyTenMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_REOMOVE_EMAIL_FROM_EMAILS)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_REOMOVE_EMAIL_FROM_EMAILS)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_REOMOVE_EMAIL_FROM_EMAILS)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        //remove api valid email
        
        $schedule->command('remove:apivalidemail')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_REOMOVE_API_VALID_EMAIL_FROM_EMAILS)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_REOMOVE_API_VALID_EMAIL_FROM_EMAILS)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_REOMOVE_API_VALID_EMAIL_FROM_EMAILS)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        //email validation cron 1
        
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
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        //email validation cron 2
        
        $schedule->command('validate:emailcron2')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_2)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_2)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_2)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        $schedule->command('validate:emailcron3')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_3)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_3)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_3)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        $schedule->command('validate:emailcron4')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_4)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_4)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_4)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        $schedule->command('validate:emailcron5')->cron('*/3 * * * *')->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_5)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_5)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_5)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        $schedule->command('validate:emailcron6')->cron('*/3 * * * *')->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_6)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_6)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_6)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        $schedule->command('validate:emailcron7')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_7)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_7)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_7)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        $schedule->command('validate:emailcron8')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_8)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_8)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_8)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        $schedule->command('validate:emailcron9')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_9)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_9)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_9)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        $schedule->command('validate:emailcron10')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_10)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_10)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_EMAIL_VALIDATION_10)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        
        $schedule->command('validate:useremail')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_IMPORT_EMAIL_VALIDATION)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_IMPORT_EMAIL_VALIDATION)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_IMPORT_EMAIL_VALIDATION)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        $schedule->command('validate:useremail2')->cron('*/3 * * * *')->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_IMPORT_EMAIL_VALIDATION_2)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_IMPORT_EMAIL_VALIDATION_2)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_IMPORT_EMAIL_VALIDATION_2)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        $schedule->command('validate:useremail3')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_IMPORT_EMAIL_VALIDATION_3)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_IMPORT_EMAIL_VALIDATION_3)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_IMPORT_EMAIL_VALIDATION_3)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        
        //formate creation cron
        $schedule->command('generate:defaultformat')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_GENERATE_DEFAULT_EMAIL_FORMAT)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_GENERATE_DEFAULT_EMAIL_FORMAT)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_GENERATE_DEFAULT_EMAIL_FORMAT)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        $schedule->command('populate:companieswithdomain')->everyFiveMinutes()->withoutOverlapping()->before(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_POPULATE_COMPANY_DATA)->get();
            $cronjobs->first()->current_status = "Running";
            $cronjobs->first()->save();
        })->after(function () {
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_POPULATE_COMPANY_DATA)->get();
            $cronjobs->first()->current_status = "Not Running";
            $cronjobs->first()->save();
        })->when(function(){
            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_POPULATE_COMPANY_DATA)->get();
            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
                return true;
            }
            return false;
        });
        
        
        
        
        //Hunter url scrapper
        
//        $schedule->command('scrapeurl:hunter')->everyFiveMinutes()->withoutOverlapping()->before(function () {
//            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_URL_HUNTER)->get();
//            $cronjobs->first()->current_status = "Running";
//            $cronjobs->first()->save();
//        })->after(function () {
//            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_URL_HUNTER)->get();
//            $cronjobs->first()->current_status = "Not Running";
//            $cronjobs->first()->save();
//        })->when(function(){
//            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_URL_HUNTER)->get();
//            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
//                return true;
//            }
//            return false;
//        });
        
        //Hunter domain scrapper
        
//        $schedule->command('scrapedomain:hunter')->everyFiveMinutes()->withoutOverlapping()->before(function () {
//            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_DOMAIN_HUNTER)->get();
//            $cronjobs->first()->current_status = "Running";
//            $cronjobs->first()->save();
//        })->after(function () {
//            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_DOMAIN_HUNTER)->get();
//            $cronjobs->first()->current_status = "Not Running";
//            $cronjobs->first()->save();
//        })->when(function(){
//            $cronjobs = CronJobs::where('cron_name', UtilConstant::CRON_SCRAPE_DOMAIN_HUNTER)->get();
//            if($cronjobs->first()->is_run == 'yes' && $cronjobs->first()->current_status == "Not Running"){
//                return true;
//            }
//            return false;
//        });
        
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
