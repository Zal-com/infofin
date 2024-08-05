<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class WeeklyNewsletter extends Mailable
{
    use Queueable, SerializesModels;

    public array $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build(): WeeklyNewsletter
    {

        return $this->view('email.newsletter', ['data' => $this->data])
            ->subject('Your Weekly Newsletter');
    }
}
