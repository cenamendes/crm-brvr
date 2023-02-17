<?php

namespace App\Repositories\Tenant\TasksReports;

use App\Models\Tenant\Tasks;
use App\Models\Tenant\Customers;
use App\Models\Tenant\TeamMember;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\TaskServices;
use App\Models\Tenant\TasksReports;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\Tenant\TasksReports\TasksReportsInterface;


class TasksReportsRepository implements TasksReportsInterface
{
    public function getTasksReports($perPage): LengthAwarePaginator
    {
        if(Auth::user()->type_user == 2)
        {
            $customer = Customers::where('user_id',Auth::user()->id)->first();
            $tasksReports = TasksReports::where('customer_id',$customer->id)->with('servicesToDo')->with('taskCustomer')->with('tech')->with('getHoursTask')->paginate($perPage);
        }
        else if(Auth::user()->type_user == 1)
        {
            $teammember = TeamMember::where('user_id',Auth::user()->id)->first();
            $tasksReports = TasksReports::where('tech_id',$teammember->id)->with('servicesToDo')->with('taskCustomer')->with('tech')->with('getHoursTask')->paginate($perPage);
        }
        else {
            $tasksReports = TasksReports::with('servicesToDo')->with('taskCustomer')->with('tech')->with('getHoursTask')->paginate($perPage);
        }

        return $tasksReports;
    }

    public function getTaskReport($searchString,$perPage): LengthAwarePaginator
    {
        if(Auth::user()->type_user == 2)
        {
            $customer = Customers::where('user_id',Auth::user()->id)->first();
            $taskReport = TasksReports::where('customer_id',$customer->id)->whereHas('servicesToDo', function ($query) use($searchString) {
                $query->WhereHas('service', function ($queryy) use($searchString) {
                    $queryy->where('name', 'like', '%' . $searchString . '%');
                });
            })
            ->orWhereHas('taskCustomer', function ($queryyy) use($searchString) {
                $queryyy->Where('short_name', 'like', '%' . $searchString . '%');
            })
            ->with('tech')
            ->with('getHoursTask')
            ->paginate($perPage);
        }
        else if(Auth::user()->type_user == 1)
        {
            $teammember = TeamMember::where('user_id',Auth::user()->id)->first();
            $taskReport = TasksReports::where('tech_id',$teammember->id)->whereHas('servicesToDo', function ($query) use($searchString) {
                $query->WhereHas('service', function ($queryy) use($searchString) {
                    $queryy->where('name', 'like', '%' . $searchString . '%');
                });
            })
            ->orWhereHas('taskCustomer', function ($queryyy) use($searchString) {
                $queryyy->Where('short_name', 'like', '%' . $searchString . '%');
            })
            ->with('tech')
            ->with('getHoursTask')
            ->paginate($perPage);
        }
        else {
            $taskReport = TasksReports::whereHas('servicesToDo', function ($query) use($searchString) {
                $query->WhereHas('service', function ($queryy) use($searchString) {
                    $queryy->where('name', 'like', '%' . $searchString . '%');
                });
            })
            ->orWhereHas('taskCustomer', function ($queryyy) use($searchString) {
                $queryyy->Where('short_name', 'like', '%' . $searchString . '%');
            })
            ->with('tech')
            ->with('getHoursTask')
            ->paginate($perPage);
        }

        return $taskReport;
    }

    public function getReport($taskReportId): TasksReports
    {
        return TasksReports::where('id',$taskReportId)->first();
    }

    public function updateReport($reportId, $taskReport): int
    {
        $report = TasksReports::where('id', $reportId)
            ->update($taskReport);

        // $reportGet =  TasksReports::where('id', $reportId)->first();   
         
        // Tasks::where('id',$reportGet->task_id)->update(["status" => $taskReport["reportStatus"]]);

        return $report;
    }

    public function getReportByTaskId($taskId): NULL|TasksReports
    {
        return TasksReports::where('task_id', $taskId)->first();
    }

    public function destroyReportByTaskId($taskId): bool
    {
        DB::beginTransaction();
        if(Tasks::where('id', $taskId)->update(['status' => 0]) == 0) {
            DB::rollBack();
            return false;
        }
        if(TasksReports::where('task_id', $taskId)->delete() == 0) {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return true;
    }

    public function updateTaskReport($tasks): int
    {
        return DB::transaction(function () use ($tasks) {
            Tasks::where('id', $tasks->id)
                ->update([
                    'status' => $tasks->status,
                    'scheduled_date' => $tasks->scheduled_date,
                    'scheduled_hour' => $tasks->scheduled_hour,
                ]);

            return TasksReports::where('task_id', $tasks->id)
                             ->update([
                                'reference' => $tasks->reference,
                                'customer_id' => $tasks->customer_id,
                                'location_id' => $tasks->location_id,
                                'task_id' => $tasks->id,
                                'additional_description' => $tasks->additional_description,
                                'applicant_name' => $tasks->applicant_name,
                                'applicant_contact' => $tasks->applicant_contact,
                                'preview_date' => $tasks->preview_date,
                                'preview_hour' => $tasks->preview_hour,
                                'scheduled_date' => $tasks->scheduled_date,
                                'scheduled_hour' => $tasks->scheduled_hour,
                                'tech_id' => $tasks->tech_id,
                            ]);

        });
    }

}
