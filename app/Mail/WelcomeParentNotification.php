<?php

namespace App\Mail;

use App\Models\Guardian;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeParentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $feedUrl;
    public string $portalName;
    public string $brandColor;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Guardian $parent,
    ) {
        $this->feedUrl = url('/feed/' . $this->parent->access_token);
        $this->portalName = Setting::where('key', 'portal_name')->value('value')
            ?? Setting::where('key', 'brand_name')->value('value')
            ?? 'Expedition Updates';
        $this->brandColor = Setting::where('key', 'brand_color')->value('value') ?? '#7413dc';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Welcome to {$this->portalName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'parent' => $this->parent,
                'feedUrl' => $this->feedUrl,
                'portalName' => $this->portalName,
                'brandColor' => $this->brandColor,
            ],
        );
    }
}
