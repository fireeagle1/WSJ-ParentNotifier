<?php

namespace App\Mail;

use App\Models\Guardian;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPostNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $truncatedContent;
    public string $feedUrl;
    public string $portalName;
    public string $leaderName;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Post $post,
        public Guardian $parent,
    ) {
        $this->post->loadMissing('leader');

        $this->leaderName = $this->post->leader->display_name;

        $this->truncatedContent = strlen($this->post->content) > 500
            ? substr($this->post->content, 0, 500) . '...'
            : $this->post->content;

        $this->feedUrl = url('/feed/' . $this->parent->access_token);

        $this->portalName = Setting::where('key', 'portal_name')->value('value') ?? 'Expedition Updates';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New update from {$this->leaderName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-post',
            with: [
                'post' => $this->post,
                'parent' => $this->parent,
                'truncatedContent' => $this->truncatedContent,
                'feedUrl' => $this->feedUrl,
                'portalName' => $this->portalName,
                'leaderName' => $this->leaderName,
            ],
        );
    }
}
