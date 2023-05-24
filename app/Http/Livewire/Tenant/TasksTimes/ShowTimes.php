<?php

namespace App\Http\Livewire\Tenant\TasksTimes;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\Tenant\TasksTimes\TasksTimesInterface;

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
    public string $hora_final = '';
    public string $descricao = '';

    protected $listeners = [
        'timesInsert'
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
        $totalDurationOfTask = $finishTime->diff($startTime)->format("%h.%i");

        $hours = date("H:i",strtotime($totalDurationOfTask));


        $newHours = (float)$totalDurationOfTask;


        $arrayInsertTask = [
            "service_id" => $this->serviceSelected,
            "task_id" => $this->task_id,
            "tech_id" => Auth::user()->id,
            "date_begin" => $this->date_inicial,
            "hour_begin" => $this->hora_inicial,
            "date_end" => $this->date_inicial,
            "hour_end" => $this->hora_final,
            "total_hours" => $hours,
            "descricao" => $this->descricao
        ];


        $this->tasksTimesInterface->addTime($arrayInsertTask);

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
