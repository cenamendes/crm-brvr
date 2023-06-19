<?php

namespace App\Listeners\Tasks;

use App\Models\Tenant\Tasks;
use App\Models\Tenant\Config;
use App\Models\Tenant\Customers;
use App\Events\Tasks\TaskCreated;
use App\Models\Tenant\TeamMember;
use App\Events\Tasks\TaskCustomer;
use App\Mail\Tasks\TaskDispatched;
use App\Mail\Tasks\TaskCreateEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\Tasks\TaskDispatchedTech;
use App\Mail\Tasks\TaskReceiveEmailUser;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Tasks\DispatchTasksToUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Tasks\TasksDispatchedNotification;

class TaskCustomerNotification
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
     * @param  \App\Events\TaskCreated  $event
     * @return void
     */
    public function handle(TaskCustomer $task)
    {     
        $customerEmail = Customers::where('id',$task->taskCustomer["customer_id"])->first();

        Mail::to($customerEmail->email)->queue(new TaskReceiveEmailUser($task));

        //criação da tarefa
        $emailConfig = Config::first();
        Mail::to($emailConfig->email)->queue(new TaskReceiveEmailUser($task));
    }
}
