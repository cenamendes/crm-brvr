<?php

namespace App\Repositories\Tenant\Tasks;

use Carbon\Carbon;
use App\Models\Tenant\Tasks;
use App\Models\Tenant\Customers;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\TaskServices;
use App\Models\Tenant\TasksReports;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\GenerateTaskReference;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\Tenant\Tasks\TasksInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\Tenant\Tasks\TasksFormRequest;
use App\Models\Tenant\TeamMember;

class TasksRepository implements TasksInterface
{
    use GenerateTaskReference;

    public function add(TasksFormRequest $request): Tasks
    {
        return DB::transaction(function () use ($request) {
            $tasks = Tasks::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_phone' => $request->mobile_phone,
                'job' => $request->job,
                'additional_info' => $request->additional_info,
            ]);
            return $tasks;
        });
    }

    public function createTask(object $values): Tasks
    {
        return DB::transaction(function () use ($values) {
           
            $task = Tasks::create([
                'number' => $values->number,
                'reference' => $values->taskReference,
                'customer_id' => $values->selectedCustomer,
                'location_id' => $values->selectedLocation,
                'additional_description' => $values->taskAdditionalDescription,
                'preview_date' => $values->previewDate,
                'preview_hour' => $values->previewHour,
                'scheduled_date' => $values->scheduledDate,
                'scheduled_hour' => $values->scheduledHour,
                'applicant_name' => ' ',
                'applicant_contact' => ' ',
                'tech_id' => $values->selectedTechnician]);

            foreach ($values->selectedServiceId as $key => $service) {
                TaskServices::create([
                    'task_id' => $task->id,
                    'task_service_id' => $key,
                    'service_id' => $service,
                    'additional_description' => $values->serviceDescription[$key],
                ]);
            }
            return Tasks::where('id', $task->id)->first();
        });
    }

    public function updateTask(Tasks $task, object $values): bool
    {
        DB::beginTransaction();
        if ($task->location_id != $values->selectedLocation) {
            if(TaskServices::where('task_id', $task->id)->delete() == 0) {
                DB::rollBack();
                return false;
            }
        }

        $update = Tasks::where('id', $task->id)
            ->update([
                'location_id' => $values->selectedLocation,
                'additional_description' => $values->taskAdditionalDescription,
                'preview_date' => $values->previewDate,
                'preview_hour' => $values->previewHour,
                'scheduled_date' => $values->scheduledDate,
                'scheduled_hour' => $values->scheduledHour,
                'tech_id' => $values->selectedTechnician]);
        if($update == 0) {
            DB::rollBack();
            return false;
        }

     
        $update = TaskServices::where('task_id', $task->id)
            ->whereNotIn('service_id', $values->selectedServiceId)
            ->delete();
      
        // if($update == 0) {
        //     DB::rollBack();
        //     return false;
        // }

        foreach ($values->selectedServiceId as $key => $service) {
            $temp = TaskServices::where(['task_id' => $task->id, 'task_service_id' => $key])->first();
            if ($temp && $temp->count() > 0) {
                $update = TaskServices::where([
                        'task_id' => $task->id,
                        'task_service_id' => $key])
                    ->update([
                        'service_id' => $service,
                        'additional_description' => $values->serviceDescription[$key],
                    ]);
                if($update == 0) {
                    DB::rollBack();
                    return false;
                }
            } else {
                $update = TaskServices::create([
                    'task_id' => $task->id,
                    'task_service_id' => $key,
                    'service_id' => $service,
                    'additional_description' => $values->serviceDescription[$key],
                ]);
                if($update == 0) {
                    DB::rollBack();
                    return false;
                }
            }
        }

       
        $update = TasksReports::create([
            'reference' => $task->reference,
            'customer_id' => $task->customer_id,
            'location_id' => $task->location_id,
            'task_id' => $task->id,
            'additional_description' => $task->additional_description,
            'applicant_name' => $task->applicant_name,
            'applicant_contact' => $task->applicant_contact,
            'preview_date' => $task->preview_date,
            'preview_hour' => $task->preview_hour,
            'scheduled_date' => $values->scheduledDate,
            'scheduled_hour' => $values->scheduledHour,
            'tech_id' => $task->tech_id
        ]);
        
         
        if($update == null) {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return true;
    }

    public function dispatchTask(Tasks $tasks): TasksReports
    {
        return DB::transaction(function () use ($tasks) {
            Tasks::where('id', $tasks->id)
                ->update([
                    'status' => $tasks->status,
                    'scheduled_date' => $tasks->scheduled_date,
                    'scheduled_hour' => $tasks->scheduled_hour,
                ]);

            return TasksReports::create([
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

    public function getTasks($perPage)
    {
        if(Auth::user()->type_user == 2)
        {
            $customer = Customers::where('user_id',Auth::user()->id)->first();
            return Tasks::where('customer_id',$customer->id)
                ->with('taskCustomer')
                ->with('taskLocation')
                ->with('servicesToDo')
                ->with('taskReports')
                ->paginate($perPage);
        }
        else if(Auth::user()->type_user == 1)
        {
            $teammember = TeamMember::where('user_id',Auth::user()->id)->first();
            return Tasks::where('tech_id',$teammember->id)
                ->with('taskCustomer')
                ->with('taskLocation')
                ->with('servicesToDo')
                ->with('taskReports')
                ->paginate($perPage);
        }
        else 
        {
            return Tasks::with('taskCustomer')
            ->with('taskLocation')
            ->with('servicesToDo')
            ->with('taskReports')
            ->paginate($perPage);
        }
    }

    public function getTaskSearch($searchString,$perPage): LengthAwarePaginator
    {
        if(Auth::user()->type_user == 2)
        {
            $customer = Customers::where('user_id',Auth::user()->id)->first();
           return Tasks::where('customer_id',$customer->id)->whereHas('servicesToDo', function ($query) use($searchString) {
                $query->WhereHas('service', function ($queryy) use($searchString) {
                    $queryy->where('name', 'like', '%' . $searchString . '%');
                });
              })
              ->orWhereHas('taskCustomer', function ($queryy) use($searchString) {
                    $queryy->Where('short_name', 'like', '%' . $searchString . '%');
              })
              ->with('taskLocation')
              ->with('taskReports')
              ->paginate($perPage);
        }
        else if(Auth::user()->type_user == 1)
        {
          $teammember = TeamMember::where('user_id',Auth::user()->id)->first();
           return Tasks::where('tech_id',$teammember->id)->whereHas('servicesToDo', function ($query) use($searchString) {
                $query->WhereHas('service', function ($queryy) use($searchString) {
                    $queryy->where('name', 'like', '%' . $searchString . '%');
                });
              })
              ->orWhereHas('taskCustomer', function ($queryy) use($searchString) {
                    $queryy->Where('short_name', 'like', '%' . $searchString . '%');
              })
              ->with('taskLocation')
              ->with('taskReports')
              ->paginate($perPage);
        }
        else {
            return Tasks::whereHas('servicesToDo', function ($query) use($searchString) {
                $query->WhereHas('service', function ($queryy) use($searchString) {
                    $queryy->where('name', 'like', '%' . $searchString . '%');
                });
              })
              ->orWhereHas('taskCustomer', function ($queryy) use($searchString) {
                    $queryy->Where('short_name', 'like', '%' . $searchString . '%');
              })
              ->with('taskLocation')
              ->with('taskReports')
              ->paginate($perPage);
        }
    }

    public function getTask($task): Tasks
    {
        return Tasks::where('id', $task->id)
            ->with('taskCustomer')
            ->with('taskLocation')
            ->with('customerServiceList')
            ->with('servicesToDo')
            ->first();
    }

    public function getTaskById($taskId): Tasks
    {
        return Tasks::where('id', $taskId)
            ->with('taskCustomer')
            ->with('taskLocation')
            ->with('customerServiceList')
            ->first();
    }

    public function deleteTask($task) {
     
        DB::beginTransaction();
        if(Tasks::where('id', $task->id)->delete() == 0) {
            DB::rollBack();
            return false;
        }
        if(TaskServices::where('task_id', $task->id)->delete() == 0) {
            DB::rollBack();
            return false;
        }

        $taskReports = TasksReports::where('reference',$task->reference)->first();
        if($taskReports != null)
        {
            if(TasksReports::where('reference', $task->reference)->delete() == 0){
                DB::rollBack();
                return false;
            }
        }
        
        DB::commit();
        return true;
    }

    public function taskCalendar(): Collection
    {
        if(Auth::user()->type_user == 2)
        {
            $customer = Customers::where('user_id',Auth::user()->id)->first();
          
            $tasks = Tasks::
             with('tech')
            ->with('taskCustomer')
            ->WhereMonth('scheduled_date', Carbon::now()->month)
            ->WhereYear('scheduled_date', Carbon::now()->year)
            ->where('customer_id',$customer->id)
            ->get();
        }
        else if(Auth::user()->type_user == 1)
        {
            $tech = TeamMember::where('user_id',Auth::user()->id)->first();
            $tasks = Tasks::
            with('tech')
           ->with('taskCustomer')
           ->WhereMonth('scheduled_date', Carbon::now()->month)
           ->WhereYear('scheduled_date', Carbon::now()->year)
           ->where('tech_id',$tech->id)
           ->get();
        }
        else {
            $tasks = Tasks::with('tech')->with('taskCustomer')
            ->whereMonth('preview_date', Carbon::now()->month)
            ->whereYear('preview_date', Carbon::now()->year)
            ->orWhereMonth('scheduled_date', Carbon::now()->month)
            ->orWhereYear('scheduled_date', Carbon::now()->year)
            ->get();
        }
       
        return $tasks;
    }

    
    public function taskCalendarMonthChange($month,$year): Collection
    {
        if(Auth::user()->type_user == 2)
        {
            $customer = Customers::where('user_id',Auth::user()->id)->first();
            $tasks = Tasks::where('customer_id',$customer->id)->with('tech')->with('taskCustomer')
            ->Where(function ($query) use($month,$year)  {
            $query->WhereMonth('scheduled_date', $month)
                    ->WhereYear('scheduled_date', $year);
            })
            ->get();
        }
        else if(Auth::user()->type_user == 1)
        {
            $tech = TeamMember::where('user_id',Auth::user()->id)->first();
            $tasks = Tasks::where('tech_id',$tech->id)->with('tech')->with('taskCustomer')
            ->Where(function ($query) use($month,$year)  {
            $query->WhereMonth('scheduled_date', $month)
                    ->WhereYear('scheduled_date', $year);
            })
            ->get();
        }
        else 
        {
            $tasks = Tasks::with('tech')->with('taskCustomer')
            ->where(function ($query) use($month,$year)  {
                $query->whereMonth('preview_date', $month)
                ->whereYear('preview_date', $year);
            })
            ->orWhere(function ($query) use($month,$year)  {
            $query->WhereMonth('scheduled_date', $month)
                    ->WhereYear('scheduled_date', $year);
            })
            ->get();
        }

        return $tasks;
    }
}
