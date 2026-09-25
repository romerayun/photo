<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data)
    {
    }

    public function envelope(): Envelope
    {
        $clientName = trim($this->data['name'] ?? 'Клиента');
        $packageName = !empty($this->data['package']) ? ' [' . $this->data['package'] . ']' : '';

        return new Envelope(
            subject: 'Новая заявка с сайта: ' . $clientName . $packageName,
            replyTo: !empty($this->data['email']) ? [$this->data['email']] : null,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form',
            text: 'emails.contact-form-text',
        );
    }
}
