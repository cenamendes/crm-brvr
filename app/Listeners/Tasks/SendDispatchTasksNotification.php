<?php

namespace App\Listeners\Tasks;

use App\Models\Tenant\Tasks;
use App\Mail\Tasks\TaskDispatched;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Tasks\DispatchTasksToUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Tasks\TasksDispatchedNotification;

class SendDispatchTasksNotification
{
    use Notifiable;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\DispatchTask  $event
     * @return void
     */
    public function handle(DispatchTasksToUser $task)
    {
        Mail::to($task->task->tech->email)->queue(new TaskDispatched($task));
        if($task->task->taskCustomer->email == null || !isset($task->task->taskCustomer->email))
        {
            Mail::to(env('MAIL_USERNAME'))->queue(new TaskDispatched($task));
        }
        else {
            Mail::to($task->task->taskCustomer->email)->queue(new TaskDispatched($task));
        }
        
    }
}
