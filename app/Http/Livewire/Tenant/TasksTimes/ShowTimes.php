<?php

namespace App\Http\Livewire\Tenant\TasksTimes;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\Tenant\TasksTimes\TasksTimesInterface;
use App\Models\Tenant\TasksTimes;

class ShowTimes extends Component
{
    use WithPagination;
    
    private TasksTimesInterface $tasksTimesInterface;
    private ?object $taskTimes =  NULL;
    public string $searchString = '';
    public int $task_id = 0;
    public ?object $taskInfo = NULL;
    public float $task_hours = 0;

    public string $serviceSelected = '';
    public string $date_inicial = '';
    public string $hora_inicial = '';
    public $hora_final;
    public string $descricao = '';

    protected $listeners = [
        'timesInsert',
        'EditTimes'
     ];

      /**
     * Livewire construct function
     *
     * @param TasksInterface $tasksInterface
     * @return Void
     */
    public function boot(TasksTimesInterface $tasksTimesInterface): Void
    {
        $this->tasksTimesInterface = $tasksTimesInterface;
    }

    public function mount($task)
    {
        $this->task_id = $task->id;
        $this->taskInfo = $task;
        $this->initProperties();
        $this->taskTimes = $this->tasksTimesInterface->getTasksTimes($this->task_id,$this->perPage);
        $this->task_hours = $this->tasksTimesInterface->getTotalHoursForTask($this->task_id);
    }

     /**
     * Change number of records to display
     *
     * @return void
     */
    public function updatedPerPage(): void
    {
        session()->put('perPage', $this->perPage);
        $this->taskTimes = $this->tasksTimesInterface->getTasksTimes($this->task_id,$this->perPage);
    }

    /**
     * Create custom pagination html string
     *
     * @return string
     */
    public function paginationView(): String
    {
        return 'tenant.livewire.setup.pagination';
    }

      /**
     * Prepare properties
     *
     * @return void
     */
    private function initProperties(): void
    {
        if (isset($this->perPage)) {
            session()->put('perPage', $this->perPage);
        } elseif (session('perPage')) {
            $this->perPage = session('perPage');
        } else {
            $this->perPage = 10;
        }
    }

    public function timesInsert()
    {
        $startTime = Carbon::parse($this->hora_inicial);
        $finishTime = Carbon::parse($this->hora_final);

        if($this->hora_final != "")
        {
            $totalDurationOfTask = $finishTime->diff($startTime)->format("%h.%i");
            $hours = date("H:i",strtotime($totalDurationOfTask));
            $date_final = $this->date_inicial;
        }
        else {
            $date_final = null;
            $hours = null;
            $this->hora_final = null;
        }
        

        //$newHours = (float)$totalDurationOfTask;


        $arrayInsertTask = [
            "service_id" => $this->serviceSelected,
            "task_id" => $this->task_id,
            "tech_id" => Auth::user()->id,
            "date_begin" => $this->date_inicial,
            "hour_begin" => $this->hora_inicial,
            "date_end" => $date_final,
            "hour_end" => $this->hora_final,
            "total_hours" => $hours,
            "descricao" => $this->descricao
        ];


        $this->tasksTimesInterface->addTime($arrayInsertTask);

    }

    public function EditTimes($id,$values)
    {
        $startTime = Carbon::parse($values[2]);
        $finishTime = Carbon::parse($values[3]);
        $totalDurationOfTask = $finishTime->diff($startTime)->format("%h.%i");

        $hours = date("H:i",strtotime($totalDurationOfTask));

        TasksTimes::where('id',$id)->update([
            "service_id" => $values[0],
            "date_begin" => $values[1],
            "hour_begin" => $values[2],
            "date_end" => $values[1],
            "hour_end" => $values[3],
            "total_hours" => $hours,
            "descricao" => $values[4]
        ]);

    }

    public function addTime()
    {

       // $services = $this->tasksTimesInterface->getAllServices();


        $message ="
        <label>".__("Service")."</label>";
        $message .= "<select name='selectedService' id='selectedService' wire:model='serviceSelected' class='form-control'>
        <option value=''>". __('Select Service')."</option>";

        foreach($this->taskInfo->servicesToDo as $serv)
        {
            $message .='<option value="'. $serv->service->id.'">'. $serv->service->name.'
            </option>';

        }

        $message .='</select>';

        $message .= "

        <label>".__('Date of Initial')."</label>
        <div class='input-group'>
        <input type='text' name='data' id='date_inicial' wire:model.defer='date_inicial' class='datepicker-default form-control'>
        <span class='input-group-append'><span class='input-group-text'>
        <i class='fa fa-calendar-o'></i>
        </span></span>
        </div>

        <label>".__('Initial Hour')."</label>
        <div class='input-group'>
        <input type='text' name='data' id='hora_inicial' wire:model.defer='hora_inicial' class='datepicker-default form-control'>
        <span class='input-group-append'><span class='input-group-text'>
        <i class='fa fa-calendar-o'></i>
        </span></span>
        </div>

        <label>".__('Final Hour')."</label>
        <div class='input-group clockpicker'>
        <input type='text' name='data' id='hora_final' wire:model.defer='hora_final' class='datepicker-default form-control'>
        <span class='input-group-append'><span class='input-group-text'>
        <i class='fa fa-calendar-o'></i>
        </span></span>
        </div>

        <label>".__('Description')."</label>
        <div class='input-group'>
        <textarea type='text' name='data' id='descricao' wire:model.defer='descricao' class='form-control' style='height:189px;'></textarea>
        </div>

       <div id='actionsDiv' style='display:flex; margin-top:20px; justify-content:center;'>
         <button type='button' id='btnAddTime' class='btn btn-primary'>Adicione</button>
         &nbsp;<button type='button' id='btnremoveTime' class='btn btn-danger'>Fechar</button>
       </div>
       ";


        $this->dispatchBrowserEvent('swal',
            [
                'title' => __('Add Time'),
                'message' => $message,
                'status'=>'info',
                'showCancelButton'=>false,
                'showConfirmButton'=>false,
                'confirmButtonColor'=> '#326c91 ',
                'confirmButtonText' => __("Add"),
                'cancelButtonText' => __("Cancel"),
                'function' => "timesInsert"
            ]);

    }

    public function editTimeTask($id)
    {
        $getInfoTaskTimes = TasksTimes::with('service')->where('id',$id)->first();

    
        $message ="
        <label>".__("Service")."</label>";
        $message .= "<select name='selectedService' id='selectedService' wire:model='serviceSelected' class='form-control'>
        <option value=''>". __('Select Service')."</option>";

        foreach($this->taskInfo->servicesToDo as $serv)
        {
            if($getInfoTaskTimes->service->id == $serv->service->id){
                $message .='<option value="'. $serv->service->id.'" selected>'. $serv->service->name.'
                </option>';
            }
            else {
                $message .='<option value="'. $serv->service->id.'">'. $serv->service->name.'
                </option>';
            }
           

        }

        $message .='</select>';

        $message .= "

        <label>".__('Date of Initial')."</label>
        <div class='input-group'>
        <input type='text' name='data' id='date_inicial' @if(".$getInfoTaskTimes->date_begin.") value='".$getInfoTaskTimes->date_begin."' @endif wire:model.defer='date_inicial' class='datepicker-default form-control'>
        <span class='input-group-append'><span class='input-group-text'>
        <i class='fa fa-calendar-o'></i>
        </span></span>
        </div>

        <label>".__('Initial Hour')."</label>
        <div class='input-group'>
        <input type='text' name='data' id='hora_inicial' @if(".$getInfoTaskTimes->hour_begin.") value='".$getInfoTaskTimes->hour_begin."' @endif wire:model.defer='hora_inicial' class='datepicker-default form-control'>
        <span class='input-group-append'><span class='input-group-text'>
        <i class='fa fa-calendar-o'></i>
        </span></span>
        </div>

        <label>".__('Final Hour')."</label>
        <div class='input-group clockpicker'>
        <input type='text' name='data' id='hora_final' @if(".$getInfoTaskTimes->hour_end.") value='".$getInfoTaskTimes->hour_end."' @endif wire:model.defer='hora_final' class='datepicker-default form-control'>
        <span class='input-group-append'><span class='input-group-text'>
        <i class='fa fa-calendar-o'></i>
        </span></span>
        </div>

        <label>".__('Description')."</label>
        <div class='input-group'>
        <textarea type='text' name='data' id='descricao' wire:model.defer='descricao' class='form-control' style='height:189px;'>".$getInfoTaskTimes->descricao."</textarea>
        </div>

       <div id='actionsDiv' style='display:flex; margin-top:20px; justify-content:center;'>
         <button type='button' id='btnEditTime' class='btn btn-primary'>Adicione</button>
         &nbsp;<button type='button' id='btnremoveTime' class='btn btn-danger'>Fechar</button>
       </div>
       ";


        $this->dispatchBrowserEvent('swal',
            [
                'title' => __('Add Time'),
                'message' => $message,
                'status'=>'info',
                'showCancelButton'=>false,
                'showConfirmButton'=>false,
                'confirmButtonColor'=> '#326c91 ',
                'confirmButtonText' => __("Edit"),
                'cancelButtonText' => __("Cancel"),
                'function' => "EditTimes",
                'parameter' => $id
            ]);
    }

    public function render()
    {
        if(isset($this->searchString) && $this->searchString) {
            $this->taskTimes = $this->tasksTimesInterface->getTaskTime($this->task_id,$this->searchString,$this->perPage);
        }
        else {
            $this->taskTimes = $this->tasksTimesInterface->getTasksTimes($this->task_id,$this->perPage);
            $this->task_hours = $this->tasksTimesInterface->getTotalHoursForTask($this->task_id);
        }
        return view('tenant.livewire.taskstimes.show-times',[
            'tasksTimes' => $this->taskTimes,
            'taskHours' => $this->task_hours
        ]);
    }
}
