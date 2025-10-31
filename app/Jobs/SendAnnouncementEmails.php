<?php

namespace App\Jobs;

use App\Mail\AnnouncementPosted;
use App\Models\AnnouncementNew;
use App\Login;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAnnouncementEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $announcementId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $announcementId)
    {
        $this->announcementId = $announcementId;
        $this->timeout = 300; // seconds
        $this->tries = 3;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $announcement = AnnouncementNew::find($this->announcementId);
        if (!$announcement) {
            return;
        }

        $emails = Login::query()
            ->where(['t_is_deleted' => 0, 't_is_active' => 1])
            ->whereNotNull('v_email')
            ->pluck('v_email')
            ->filter(function ($e) { return filter_var($e, FILTER_VALIDATE_EMAIL); })
            ->unique()
            ->values()
            ->all();

        if (empty($emails)) {
            return;
        }

        $chunkSize = 50;
        $fromAddress = config('mail.from.address');

        if (empty($fromAddress)) {
            foreach ($emails as $email) {
                Mail::to($email)->send(new AnnouncementPosted($announcement));
            }
            return;
        }

        foreach (array_chunk($emails, $chunkSize) as $bccList) {
            Mail::to($fromAddress)
                ->bcc($bccList)
                ->send(new AnnouncementPosted($announcement));
        }
    }
}
