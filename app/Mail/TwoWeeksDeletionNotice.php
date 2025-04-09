<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ThreeMonthDeletionNotice extends Mailable
{
    use Queueable, SerializesModels;

    public array $data = [];

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build(): ThreeMonthDeletionNotice {
        return $this->view('emails.three_months_deletion_notice', ['data' => $this->data])
            ->subject('Infofin - Votre compte sera bientôt supprimé.');
    }
}
