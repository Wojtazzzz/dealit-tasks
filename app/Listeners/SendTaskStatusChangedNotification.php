<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TaskStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTaskStatusChangedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskStatusChanged $event): void
    {
        Mail::to($event->user->email)->queue(new \App\Mail\TaskStatusChanged());
    }
}
