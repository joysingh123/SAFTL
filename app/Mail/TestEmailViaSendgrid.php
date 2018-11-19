<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestEmailViaSendgrid extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public $data;
    
    public function __construct($data)
    {
       $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = 'ajay.agrawal9022@gmail.com';
        $subject = 'Test Email!';
        $name = 'Ajay Agrawal';
        return $this->view('emails.test')->from($address, $name)->replyTo($address, $name)->subject($subject);
    }
}
