<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewlsetterTest extends Mailable
{
    use Queueable, SerializesModels;

    public array $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build(): NewlsetterTest
    {
        return $this->subject('Test newsletter infofin')->text('emails.newsletter-test');
    }
}
