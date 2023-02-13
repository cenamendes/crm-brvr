<?php

namespace App\Listeners\Tasks;

use App\Events\Tasks\DispatchTaskReport;
use App\Mail\Tasks\TaskReportFinished;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTaskReportNotification
{
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
     * @param  \App\Events\DispatchTaskReport  $event
     * @return void
     */
    public function handle(DispatchTaskReport $tasksReports)
    {
        $tasksReports = $tasksReports->tasksReports;
        Mail::to($tasksReports->tech->email)->queue(new TaskReportFinished($tasksReports));
        if($tasksReports->taskCustomer->email == null || !isset($tasksReports->taskCustomer->email))
        {
            Mail::to(env('MAIL_USERNAME'))->queue(new TaskReportFinished($tasksReports));
        }
        else {
            Mail::to($tasksReports->taskCustomer->email)->queue(new TaskReportFinished($tasksReports));
        }
        //Mail::to($tasksReports->taskCustomer->email)->queue(new TaskReportFinished($tasksReports));
    }
}
