<?php

namespace App\Jobs;

use App\Notifications\SendInviteNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Notification;

class SendInviteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $email,
        public string $inviterName,
        public string $todoName,
        public string $inviteUrl
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Notification::route('mail', $this->email)
            ->notify(new SendInviteNotification($this->inviterName, $this->todoName, $this->inviteUrl));
    }
}
