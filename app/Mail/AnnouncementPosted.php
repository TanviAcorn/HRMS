<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\AnnouncementNew;

class AnnouncementPosted extends Mailable
{
    use Queueable, SerializesModels;

    public $announcement;

    /**
     * Create a new message instance.
     */
    public function __construct(AnnouncementNew $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Announcement: ' . ($this->announcement->title ?? ''))
            ->view('emails.announcement_posted');
    }
}
