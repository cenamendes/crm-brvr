<?php

namespace App\Repositories\Tenant\Tasks;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Tenant\Tasks;
use App\Models\Tenant\Customers;
use App\Models\Tenant\TeamMember;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\SerieNumbers;
use App\Models\Tenant\TaskServices;
use App\Models\Tenant\TasksReports;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\GenerateTaskReference;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\Tenant\Tasks\TasksInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\Tenant\Tasks\TasksFormRequest;
use App\Models\Tenant\Departamentos;

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

           if(!isset($values->alert_email))
           {
               $email_alert = 0;
           }
           else {
               $email_alert = 1;
           }

        //    if(!isset($values->selectPrioridade))
        //    {
        //       $values->selectPrioridade = 1;
        //    }
          
            $task = Tasks::create([
                'number' => $values->number,
                'reference' => $values->taskReference,
                'customer_id' => $values->selectedCustomer,
                'location_id' => $values->selectedLocation,
                'resume' => $values->resume,
                'additional_description' => $values->taskAdditionalDescription,
                'preview_date' => $values->previewDate,
                'preview_hour' => $values->previewHour,
                'scheduled_date' => $values->scheduledDate,
                'scheduled_hour' => $values->scheduledHour,
                'applicant_name' => ' ',
                'applicant_contact' => ' ',
                'tech_id' => $values->selectedTechnician,
                'origem_pedido' => $values->origem_pedido,
                'quem_pediu' => $values->quem_pediu,
                'tipo_pedido' => $values->tipo_pedido,
                'alert_email' => $email_alert,
                'nr_serie' => $values->serieNumber,
                'marca' => $values->marcaEquipment,
                'modelo' => $values->modelEquipment,
                'nome_equipamento' => $values->nameEquipment,
                'descricao_equipamento' => $values->descriptionEquipment,
                'riscado' => $values->riscado,
                'partido' => $values->partido,
                'bom_estado' => $values->bomestado,
                'estado_normal' => $values->normalestado,
                'transformador' => $values->transformador,
                'mala' => $values->mala,
                'tinteiro' => $values->tinteiro,
                'ac' => $values->ac,
                'descricao_extra' => $values->descriptionExtra,
                'imagem' => $values->imagem,
                'prioridade' => $values->selectPrioridade
            ]);

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


        if($values->imagem != "")
        {
            $imagem = $values->imagem;
        }
        else {
            $imagem = "";
        }

        $update = Tasks::where('id', $task->id)
            ->update([
                'location_id' => $values->selectedLocation,
                'resume' => $values->resume,
                'additional_description' => $values->taskAdditionalDescription,
                'preview_date' => $values->previewDate,
                'preview_hour' => $values->previewHour,
                'scheduled_date' => $values->scheduledDate,
                'scheduled_hour' => $values->scheduledHour,
                'tech_id' => $values->selectedTechnician,
                'origem_pedido' => $values->origem_pedido,
                'quem_pediu' => $values->quem_pediu,
                'tipo_pedido' => $values->tipo_pedido,
                'alert_email' => $values->alert_email,
                'nr_serie' => $values->serieNumber,
                'marca' => $values->marcaEquipment,
                'modelo' => $values->modelEquipment,
                'nome_equipamento' => $values->nameEquipment,
                'descricao_equipamento' => $values->descriptionEquipment,
                'riscado' => $values->riscado,
                'partido' => $values->partido,
                'bom_estado' => $values->bomestado,
                'estado_normal' => $values->normalestado,
                'transformador' => $values->transformador,
                'mala' => $values->mala,
                'tinteiro' => $values->tinteiro,
                'ac' => $values->ac,
                'descricao_extra' => $values->descriptionExtra,
                'imagem' => $imagem,
                'prioridade' => $values->selectPrioridade
                ]);
        
        $taskReportUpdate = TasksReports::where('task_id', $task->id)
                             ->update([
                                'preview_date' => $values->previewDate,
                                'preview_hour' => $values->previewHour,
                                'scheduled_date' => $values->scheduledDate,
                                'scheduled_hour' => $values->scheduledHour,
                                'tech_id' => $values->selectedTechnician
                             ]);
        
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
               
               if($update->count() == 0)
               {
                    $update = 0;
                    if($update == 0) {
                        DB::rollBack();
                        return false;
                    }
               }
               else {
                   DB::commit();
                   return true;
               } 
               
                // if($update == 0) {
                //     DB::rollBack();
                //     return false;
                // }
            }
        }

       
        // $update = TasksReports::create([
        //     'reference' => $task->reference,
        //     'customer_id' => $task->customer_id,
        //     'location_id' => $task->location_id,
        //     'task_id' => $task->id,
        //     'additional_description' => $task->additional_description,
        //     'applicant_name' => $task->applicant_name,
        //     'applicant_contact' => $task->applicant_contact,
        //     'preview_date' => $task->preview_date,
        //     'preview_hour' => $task->preview_hour,
        //     'scheduled_date' => $values->scheduledDate,
        //     'scheduled_hour' => $values->scheduledHour,
        //     'tech_id' => $task->tech_id
        // ]);
        
         
        // if($update == null) {
        //     DB::rollBack();
        //     return false;
        // }
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
                ->orderBy('created_at','desc')
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
                ->orderBy('created_at','desc')
                ->paginate($perPage);
        }
        else 
        {
            return Tasks::with('taskCustomer')
            ->with('taskLocation')
            ->with('servicesToDo')
            ->with('taskReports')
            ->orderBy('created_at','desc')
            ->paginate($perPage);
        }
    }


    public function getTaskSearch($searchString,$perPage): LengthAwarePaginator
    {
        if(Auth::user()->type_user == 2)
        {

            $customer = Customers::where('user_id',Auth::user()->id)->first();
           return Tasks::where('customer_id',$customer->id)
           ->where(function($query) use($searchString){
                $query->whereHas('servicesToDo', function ($query) use($searchString) {
                    $query->WhereHas('service', function ($queryy) use($searchString) {
                        $queryy->where('name', 'like', '%' . $searchString . '%');
                    });
                })
                ->orWhereHas('taskCustomer', function ($queryy) use($searchString) {
                        $queryy->Where('short_name', 'like', '%' . $searchString . '%');
                });
           })
          
              ->with('taskLocation')
              ->with('taskReports')
              ->orderBy('created_at','desc')
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
          
            // $tasks = Tasks::
            //  with('tech')
            // ->with('taskCustomer')
            // ->WhereMonth('scheduled_date', Carbon::now()->month)
            // ->WhereYear('scheduled_date', Carbon::now()->year)
            // ->where('customer_id',$customer->id)
            // ->get();

            $tasks = Tasks::with('tech')
                     ->with('taskCustomer')
                     ->with('tasksTimes')
                     ->where(function ($query) {
                        $query->where(function ($query) {
                            $query->whereMonth('preview_date', Carbon::now()->month)
                                    ->whereYear('preview_date', Carbon::now()->year);
                        })
                        ->orWhere(function ($query) {
                            $query->whereMonth('scheduled_date', Carbon::now()->month)
                                  ->whereYear('scheduled_date', Carbon::now()->year);
                        });
                     })
                     ->where('customer_id',$customer->id)
                     ->get();
        }
        else if(Auth::user()->type_user == 1)
        {
        //     $tech = TeamMember::where('user_id',Auth::user()->id)->first();

        //    $tasks = Tasks::
        //    with('tech')
        //    ->with('taskCustomer')
        //    ->with('tasksTimes')
        //    ->where(function ($query) {
        //        $query->where(function ($query) {
        //                    $query->whereMonth('preview_date', Carbon::now()->month)
        //                            ->whereYear('preview_date', Carbon::now()->year);
        //                    })
        //            ->orWhere(function ($query) {
        //                $query->WhereMonth('scheduled_date', Carbon::now()->month)
        //                        ->WhereYear('scheduled_date', Carbon::now()->year);
        //        });
        //    })  
        //    ->where('tech_id',$tech->id)
        //    ->get();


        $tech = TeamMember::where('user_id',Auth::user()->id)->first();


            if($tech->id_hierarquia == "2")
            {
                $depts = Departamentos::where('id',$tech->id_departamento)->first();

                $tMFromDepartment = TeamMember::where('id_departamento', $depts->id)->get();

                $tasks = Tasks::
                with('tech')
                ->with('taskCustomer')
                ->where(function ($query) {
                    $query->where(function ($query) {
                                $query->whereMonth('preview_date', Carbon::now()->month)
                                        ->whereYear('preview_date', Carbon::now()->year);
                                })
                        ->orWhere(function ($query) {
                            $query->WhereMonth('scheduled_date', Carbon::now()->month)
                                    ->WhereYear('scheduled_date', Carbon::now()->year);
                    });
                });

                foreach($tMFromDepartment as $dep)
                {
                    $tasks->orwhere('tech_id',$dep->id);
                }
                $tasks->where('tech_id',$tech->id);
                $tasks = $tasks->get();
            }
            else
            {
                $tasks = Tasks::
                with('tech')
                ->with('taskCustomer')
                ->where(function ($query) {
                    $query->where(function ($query) {
                                $query->whereMonth('preview_date', Carbon::now()->month)
                                        ->whereYear('preview_date', Carbon::now()->year);
                                })
                        ->orWhere(function ($query) {
                            $query->WhereMonth('scheduled_date', Carbon::now()->month)
                                    ->WhereYear('scheduled_date', Carbon::now()->year);
                    });
                });

                $tasks->where('tech_id',$tech->id);
                $tasks = $tasks->get();
            }

        }
        else {
            $tasks = Tasks::with('tech')->with('taskCustomer')
            ->with('tasksTimes')
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

            //$tasks = Tasks::where('customer_id',$customer->id)->with('tech')->with('taskCustomer')
            //->Where(function ($query) use($month,$year)  {
            //$query->WhereMonth('scheduled_date', $month)
            //        ->WhereYear('scheduled_date', $year);
            //})
            //->get();

            $tasks = Tasks::
                     with('tech')
                     ->with('taskCustomer')
                     ->where(function ($query) use ($month,$year) {
                             $query->where(function ($query) use ($month,$year) {
                                $query->whereMonth('preview_date', $month)
                                      ->whereYear('preview_date', $year);
                             })
                             ->orWhere(function ($query) use ($month,$year) {
                                $query->whereMonth('scheduled_date', $month)
                                      ->whereYear('scheduled_date', $year);
                             });
                     })
                     ->where('customer_id',$customer->id)
                     ->get();
        }
        else if(Auth::user()->type_user == 1)
        {
            $tech = TeamMember::where('user_id',Auth::user()->id)->first();


            if($tech->id_hierarquia == "2")
            {
                $depts = Departamentos::where('id',$tech->id_departamento)->first();

                $tMFromDepartment = TeamMember::where('id_departamento', $depts->id)->get();

                $tasks = Tasks::
                with('tech')
                ->with('taskCustomer')
                ->where(function ($query) use ($month,$year) {
                    $query->where(function($query) use ($month,$year) {
                        $query->whereMonth('preview_date',$month)
                              ->whereYear('preview_date',$year);
                    })
                    ->orWhere(function ($query) use ($month,$year) {
                       $query->whereMonth('scheduled_date', $month)
                             ->whereYear('scheduled_date',$year);
                    });
                });

                foreach($tMFromDepartment as $dep)
                {
                    $tasks->orwhere('tech_id',$dep->id);
                }
                $tasks->where('tech_id',$tech->id);
                $tasks = $tasks->get();

            }
            else
            {
                $tasks = Tasks::
                with('tech')
                ->with('taskCustomer')
                ->where(function ($query) use ($month,$year) {
                    $query->where(function($query) use ($month,$year) {
                        $query->whereMonth('preview_date',$month)
                              ->whereYear('preview_date',$year);
                    })
                    ->orWhere(function ($query) use ($month,$year) {
                       $query->whereMonth('scheduled_date', $month)
                             ->whereYear('scheduled_date',$year);
                    });
                })
                ->where('tech_id',$tech->id)
                ->get();
            }
           
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


    /**FILTRO */

    public function getTasksFilter($searchString,$tech,$client,$typeReport,$work,$ordenation,$dateBegin,$dateEnd,$perPage): LengthAwarePaginator
    {          
        if($client != 0)
        {
            $tasks = Tasks::whereHas('tech', function ($query) use ($tech)
            {
               if($tech != 0)
               {
                   $query->where('id',$tech);
               }
            })
            ->whereHas('servicesToDo', function ($query) use ($work, $searchString)
            {
                $query->WhereHas('service', function ($queryy) use($work, $searchString) {

                    if($work != 0)
                    {
                         $queryy->where('id',$work);
                    }

                    if($searchString != "")
                    {
                        $queryy->where('name', 'like', '%' . $searchString . '%');
                    }

                });
              
            })
            ->whereHas('taskCustomer', function ($query) use ($searchString)
            {
                if($searchString != "")
                {
                    $query->where('short_name', 'like', '%' . $searchString . '%');
                }
            });
            
            if($typeReport != 3 && $typeReport != 4 )
            {
                $tasks = $tasks->whereHas('taskReports', function ($query) use ($typeReport){
                    if($typeReport != 4)
                    {
                        $query->where('reportStatus',$typeReport);
                    }
                })
                ->when($dateBegin != "" && $dateEnd != "", function($query) use($dateBegin,$dateEnd) {
                    $query->where('scheduled_date','>=',$dateBegin)->where('scheduled_date','<=',$dateEnd);
                })
                ->when($dateBegin != "" && $dateEnd == "", function($query) use($dateBegin) {
                    $query->where('scheduled_date','>=',$dateBegin);
                })
                ->when($dateBegin == "" && $dateEnd != "", function($query) use ($dateEnd) {
                    $query->where('scheduled_date','<=',$dateEnd);
                });
    
                if(Auth::user()->type_user == 2)
                {
                   $customer = Customers::where('user_id',Auth::user()->id)->first();
                   $tasks = $tasks->where('customer_id',$customer->id);
                }
                $tasks = $tasks->where('customer_id',$client);
    
                if($ordenation == "asc"){
                    $tasks = $tasks->orderBy('created_at', 'asc')->paginate($perPage);
                 }
                 else {
                    $tasks = $tasks->orderBy('created_at','desc')->paginate($perPage);
                 }
            }
            else if($typeReport == 4)
            {
                $tasks = $tasks
                ->when($dateBegin != "" && $dateEnd != "", function($query) use($dateBegin,$dateEnd) {
                    $query->where('scheduled_date','>=',$dateBegin)->where('scheduled_date','<=',$dateEnd);
                })
                ->when($dateBegin != "" && $dateEnd == "", function($query) use($dateBegin) {
                    $query->where('scheduled_date','>=',$dateBegin);
                })
                ->when($dateBegin == "" && $dateEnd != "", function($query) use ($dateEnd) {
                    $query->where('scheduled_date','<=',$dateEnd);
                });
    
                if(Auth::user()->type_user == 2)
                {
                   $customer = Customers::where('user_id',Auth::user()->id)->first();
                   $tasks = $tasks->where('customer_id',$customer->id);
                }
                $tasks = $tasks->where('customer_id',$client);
    
                if($ordenation == "asc"){
                    $tasks = $tasks->orderBy('created_at', 'asc')->paginate($perPage);
                 }
                 else {
                    $tasks = $tasks->orderBy('created_at','desc')->paginate($perPage);
                 }
            }
            else
            {
                $tasks = $tasks->where('scheduled_date',null)
                ->when($dateBegin != "" && $dateEnd != "", function($query) use($dateBegin,$dateEnd) {
                    $query->where('scheduled_date','>=',$dateBegin)->where('scheduled_date','<=',$dateEnd);
                })
                ->when($dateBegin != "" && $dateEnd == "", function($query) use($dateBegin) {
                    $query->where('scheduled_date','>=',$dateBegin);
                })
                ->when($dateBegin == "" && $dateEnd != "", function($query) use ($dateEnd) {
                    $query->where('scheduled_date','<=',$dateEnd);
                });
    
                if(Auth::user()->type_user == 2)
                {
                   $customer = Customers::where('user_id',Auth::user()->id)->first();
                   $tasks = $tasks->where('customer_id',$customer->id);
                }
                $tasks = $tasks->where('customer_id',$client);
    
                if($ordenation == "asc"){
                    $tasks = $tasks->orderBy('created_at', 'asc')->paginate($perPage);
                 }
                 else {
                    $tasks = $tasks->orderBy('created_at','desc')->paginate($perPage);
                 }
            }
            // ->whereHas('taskReports', function ($query) use ($typeReport){
            //     if($typeReport != 4)
            //     {
            //         $query->where('reportStatus',$typeReport);
            //     }
            // })
            // ->when($dateBegin != "" && $dateEnd != "", function($query) use($dateBegin,$dateEnd) {
            //     $query->where('scheduled_date','>=',$dateBegin)->where('scheduled_date','<=',$dateEnd);
            // })
            // ->when($dateBegin != "" && $dateEnd == "", function($query) use($dateBegin) {
            //     $query->where('scheduled_date','>=',$dateBegin);
            // })
            // ->when($dateBegin == "" && $dateEnd != "", function($query) use ($dateEnd) {
            //     $query->where('scheduled_date','<=',$dateEnd);
            // })
            // $tasks = $tasks->where('customer_id',$client);

            // if(Auth::user()->type_user == 2)
            // {
            //    $customer = Customers::where('user_id',Auth::user()->id)->first();
            //    $tasks = $tasks->where('customer_id',$customer->id);
            // }

            // if($ordenation == "asc"){
            //     $tasks = $tasks->orderBy('created_at', 'asc')->paginate($perPage);
            //  }
            //  else {
            //     $tasks = $tasks->orderBy('created_at','desc')->paginate($perPage);
            //  }
            
            
        }
        else 
        {
            $tasks = Tasks::whereHas('tech', function ($query) use ($tech)
            {
               if($tech != 0)
               {
                   $query->where('id',$tech);
               }
            })
            ->whereHas('taskCustomer', function ($query) use ($searchString)
            {
                if($searchString != "")
                {
                    $query->where('short_name', 'like', '%' . $searchString . '%');
                }
            })
            ->whereHas('servicesToDo', function ($query) use ($work, $searchString)
            {
                $query->WhereHas('service', function ($queryy) use($work, $searchString) {

                    if($work != 0)
                    {
                         $queryy->where('id',$work);
                    }
                    if($searchString != "")
                    {
                        $queryy->orwhere('name', 'like', '%' . $searchString . '%');
                    }

                });
              
            });

            if($typeReport != 3 && $typeReport != 4 )
            {
                $tasks = $tasks->whereHas('taskReports', function ($query) use ($typeReport){
                    if($typeReport != 4)
                    {
                        $query->where('reportStatus',$typeReport);
                    }
                })
                ->when($dateBegin != "" && $dateEnd != "", function($query) use($dateBegin,$dateEnd) {
                    $query->where('scheduled_date','>=',$dateBegin)->where('scheduled_date','<=',$dateEnd);
                })
                ->when($dateBegin != "" && $dateEnd == "", function($query) use($dateBegin) {
                    $query->where('scheduled_date','>=',$dateBegin);
                })
                ->when($dateBegin == "" && $dateEnd != "", function($query) use ($dateEnd) {
                    $query->where('scheduled_date','<=',$dateEnd);
                });
    
                if(Auth::user()->type_user == 2)
                {
                   $customer = Customers::where('user_id',Auth::user()->id)->first();
                   $tasks = $tasks->where('customer_id',$customer->id);
                }
    
                if($ordenation == "asc"){
                    $tasks = $tasks->orderBy('created_at', 'asc')->paginate($perPage);
                 }
                 else {
                    $tasks = $tasks->orderBy('created_at','desc')->paginate($perPage);
                 }
            }
            else if($typeReport == 4)
            {
                $tasks = $tasks
                ->when($dateBegin != "" && $dateEnd != "", function($query) use($dateBegin,$dateEnd) {
                    $query->where('scheduled_date','>=',$dateBegin)->where('scheduled_date','<=',$dateEnd);
                })
                ->when($dateBegin != "" && $dateEnd == "", function($query) use($dateBegin) {
                    $query->where('scheduled_date','>=',$dateBegin);
                })
                ->when($dateBegin == "" && $dateEnd != "", function($query) use ($dateEnd) {
                    $query->where('scheduled_date','<=',$dateEnd);
                });
    
                if(Auth::user()->type_user == 2)
                {
                   $customer = Customers::where('user_id',Auth::user()->id)->first();
                   $tasks = $tasks->where('customer_id',$customer->id);
                }
    
                if($ordenation == "asc"){
                    $tasks = $tasks->orderBy('created_at', 'asc')->paginate($perPage);
                 }
                 else {
                    $tasks = $tasks->orderBy('created_at','desc')->paginate($perPage);
                 }
            }
            else
            {
                $tasks = $tasks->where('scheduled_date',null)
                ->when($dateBegin != "" && $dateEnd != "", function($query) use($dateBegin,$dateEnd) {
                    $query->where('scheduled_date','>=',$dateBegin)->where('scheduled_date','<=',$dateEnd);
                })
                ->when($dateBegin != "" && $dateEnd == "", function($query) use($dateBegin) {
                    $query->where('scheduled_date','>=',$dateBegin);
                })
                ->when($dateBegin == "" && $dateEnd != "", function($query) use ($dateEnd) {
                    $query->where('scheduled_date','<=',$dateEnd);
                });
    
                if(Auth::user()->type_user == 2)
                {
                   $customer = Customers::where('user_id',Auth::user()->id)->first();
                   $tasks = $tasks->where('customer_id',$customer->id);
                }
    
                if($ordenation == "asc"){
                    $tasks = $tasks->orderBy('created_at', 'asc')->paginate($perPage);
                 }
                 else {
                    $tasks = $tasks->orderBy('created_at','desc')->paginate($perPage);
                 }
            }

         
            // ->when($dateBegin != "" && $dateEnd != "", function($query) use($dateBegin,$dateEnd) {
            //     $query->where('scheduled_date','>=',$dateBegin)->where('scheduled_date','<=',$dateEnd);
            // })
            // ->when($dateBegin != "" && $dateEnd == "", function($query) use($dateBegin) {
            //     $query->where('scheduled_date','>=',$dateBegin);
            // })
            // ->when($dateBegin == "" && $dateEnd != "", function($query) use ($dateEnd) {
            //     $query->where('scheduled_date','<=',$dateEnd);
            // });

            // if(Auth::user()->type_user == 2)
            // {
            //    $customer = Customers::where('user_id',Auth::user()->id)->first();
            //    $tasks = $tasks->where('customer_id',$customer->id);
            // }

            // if($ordenation == "asc"){
            //     $tasks = $tasks->orderBy('created_at', 'asc')->paginate($perPage);
            //  }
            //  else {
            //     $tasks = $tasks->orderBy('created_at','desc')->paginate($perPage);
            //  }
        
        }
       
        
        return $tasks;
    }


    /**FIM FILTRO */

    public function searchSerialNumber($serialNumber): LengthAwarePaginator
    {
        //Fazer sempre uma pesquisa com where e vou retornar os 2 valores
        $collection = SerieNumbers::where('nr_serie','like', '%'.$serialNumber.'%')->paginate(1);

        return $collection;

    }


}
