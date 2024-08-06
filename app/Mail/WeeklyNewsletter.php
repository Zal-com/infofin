<?php

namespace App\Mail;

use DayLaborers\LaravelMjml\Mail\MjmlMailable;
use Faker\Core\File;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Spatie\Mjml\Mjml;

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
            ->subject('Votre newsletter Infofin');
    }
}
