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
        'App\Console\Commands\MatchedContacts'
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
        $schedule->command('create:matchedcontact')->everyMinute()->withoutOverlapping()->before(function () {
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
       $schedule->command('generate:emailformat')->everyMinute()->withoutOverlapping()->before(function () {
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
        
        $schedule->command('create:email')->everyMinute()->withoutOverlapping()->before(function () {
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
