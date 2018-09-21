<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;
use App\Emails;
use App\EmailValidation;
use App\Helpers\UtilDebug;
use App\Helpers\UtilString;

class ValidateEmail extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate Created Email';

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
        UtilDebug::debug("start processing");
        $emails = Emails::where('status', 'success')->take(50)->get();
        foreach ($emails AS $email) {
            $email_for_validation = $email->email;
            $id = $email->id;
            $url = "http://apilayer.net/api/check?access_key=dd222a1917da268b6543203b4e98f9a3&email=$email_for_validation&smtp=1&format=1";
            $response = Curl::to($url)->get();
            $response_array = json_decode($response, TRUE);
            if (isset($response_array['email'])) {
                $is_validate = EmailValidation::where('email_id', $id)->get();
                if ($is_validate->count() <= 0) {
                    $email_validate = new EmailValidation();
                    $email_validate->email_id = $id;
                    $email_validate->did_you_mean = $response_array['did_you_mean'];
                    $email_validate->format_valid = $response_array['format_valid'];
                    $email_validate->mx_found = $response_array['mx_found'];
                    $email_validate->score = $response_array['score'];
                    $email_validate->raw_data = $response;
                    $email_validate->save();
                    if ($email_validate->mx_found) {
                        $email->status = 'valid';
                        $email->save();
                    } else {
                        $email->status = 'invalid';
                        $email->save();
                    }
                }
            }
        }
        UtilDebug::debug("End processing");
    }

}
