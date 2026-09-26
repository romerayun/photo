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
        $methodLabel = !empty($this->data['contact_method_label']) ? ' (' . $this->data['contact_method_label'] . ')' : '';

        $replyTo = (!empty($this->data['email']) && filter_var($this->data['email'], FILTER_VALIDATE_EMAIL))
            ? [$this->data['email']]
            : null;

        return new Envelope(
            subject: 'Новая заявка с сайта: ' . $clientName . $packageName . $methodLabel,
            replyTo: $replyTo,
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
