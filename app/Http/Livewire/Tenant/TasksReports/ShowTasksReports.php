<?php

namespace App\Http\Livewire\Tenant\TasksReports;

use App\Models\User;
use Livewire\Component;
use App\Events\ChatMessage;
use Livewire\WithPagination;
use App\Models\Tenant\TasksTimes;
use App\Models\Tenant\TasksReports;
use Illuminate\Support\Facades\Auth;
use App\Events\Tasks\DispatchTaskReport;
use App\Interfaces\Tenant\Customers\CustomersInterface;
use App\Interfaces\Tenant\TeamMember\TeamMemberInterface;
use App\Interfaces\Tenant\Setup\Services\ServicesInterface;
use App\Interfaces\Tenant\TasksReports\TasksReportsInterface;

class ShowTasksReports extends Component
{

    use WithPagination;

    protected $listeners = ["taskContinue" => "taskContinue"];

    private TasksReportsInterface $tasksReportsInterface;
    private ?object $tasksReportsList = NULL;
    public string $searchString = '';

    /** Inicio do Filtro */

    protected object $analysisRepository;
    protected object $teamMembersRepository;
    protected object $customersRepository;
    protected object $serviceRepository;

    private ?object $analysis = NULL;
    private ?object $members = NULL;
    private ?object $customers = NULL;
    private ?object $service = NULL;
    private ?object $analysisToExcel = NULL;

    public int $technical = 0;
    public int $client = 0;
    public int $work = 0;
    public int $typeTask = 4;
    public string $ordenation = '';

    public string $dateBegin = '';
    public string $dateEnd = '';
 
     public int $flagRender = 0;

    /*****FIM DO FILTRO*****/


     /**
     * Livewire construct function
     *
     * @param TasksInterface $tasksInterface
     * @return Void
     */
    public function boot(TasksReportsInterface $tasksReportsInterface, TeamMemberInterface $interfaceTeamMember, CustomersInterface $interfaceCustomers, ServicesInterface $interfaceService): Void
    {
        $this->tasksReportsInterface = $tasksReportsInterface;

        //Parte do filtro

        $this->teamMembersRepository = $interfaceTeamMember;
        $this->customersRepository = $interfaceCustomers;
        $this->serviceRepository = $interfaceService;

        //Fim da parte do filtro
    }

    /**
     * Livewire mount properties
     *
     * @return void
     */
    public function mount(): Void
    {
        $this->initProperties();

        /** Parte do Filtro */

        $this->members = $this->teamMembersRepository->getTeamMembersAnalysis();
        $this->customers = $this->customersRepository->getCustomersAnalysis();
        $this->service = $this->serviceRepository->getServicesAnalysis();


        //**Fim do Filtro****** */

    }

    public function taskContinue($reportId,$taskReport): void
    {
         $update['reportStatus'] = 2;
         $update['end_date'] = date('Y-m-d');
         $update['end_hour'] = date('H:i:s');
         $this->tasksReportsInterface->updateReport($reportId, $update);
         event(new DispatchTaskReport(TasksReports::where('id',$taskReport["id"])->with('servicesToDo')->with('tasks')->with('tech')->with("taskCustomer")->with('taskLocation')->with('getHoursTask')->first()));

         $this->dispatchBrowserEvent('swal', ['title' => __('Task Report'), 'message' => __('Task report closed with sucess!'), 'status'=>'info']);
    }

    /**Funções da parte do filtro */

    public function updatedTechnical(): void
    {
        $this->flagRender = 1;
    }

    public function updatedClient(): void
    {
        $this->flagRender = 1;
    }

    public function updatedWork(): void
    {
        $this->flagRender = 1;
    }

    public function updatedTypeTask(): void
    {
        $this->flagRender = 1;
    }

    public function updatedOrdenation(): void
    {
        $this->flagRender = 1;
    }

    public function updatedDateBegin(): void
    {
        $this->dispatchBrowserEvent("contentChanged");

        $this->flagRender = 1; 
    }

    public function updatedDateEnd(): void
    {
        $this->dispatchBrowserEvent("contentChanged");

        $this->flagRender = 1;
        $this->resetPage();
    }

    public function clearFilter(): void
    {
        $this->members = $this->teamMembersRepository->getTeamMembersAnalysis();
        $this->customers = $this->customersRepository->getCustomersAnalysis();
        $this->service = $this->serviceRepository->getServicesAnalysis();

        $this->technical = 0;
        $this->client = 0;
        $this->work = 0;
        $this->typeTask = 4;
        $this->ordenation = 'desc';
        $this->dateBegin = '';
        $this->dateEnd = '';

        $this->flagRender = 0;
    }

    /**Fim das funções do filtro*/ 




     /**
     * Change number of records to display
     *
     * @return void
     */
    public function updatedPerPage(): void
    {
        session()->put('perPage', $this->perPage);
        $this->tasksReportsList = $this->tasksInterface->getTasks($this->perPage);
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

    public function render()
    {
        /** Filtro */

        $this->members = $this->teamMembersRepository->getTeamMembersAnalysis();
        $this->customers = $this->customersRepository->getCustomersAnalysis();
        $this->service = $this->serviceRepository->getServicesAnalysis();

        /** Final do filtro */

        // if(isset($this->searchString) && $this->searchString) {
        //     $this->tasksReportsList = $this->tasksReportsInterface->getTaskReport($this->searchString,$this->perPage);
        // } else {
        //     $this->tasksReportsList = $this->tasksReportsInterface->getTasksReports($this->perPage);
        // }

        /** Parte do Filtro */

        if(isset($this->searchString) && $this->searchString)
        {
            if($this->flagRender == 0)
            {
                $this->tasksReportsList = $this->tasksReportsInterface->getTaskReport($this->searchString, $this->perPage);
            }
            else
            {
                $this->tasksReportsList = $this->tasksReportsInterface->getTasksReportsFilter($this->searchString, $this->technical, $this->client,$this->typeTask, $this->work, $this->ordenation,$this->dateBegin,$this->dateEnd,$this->perPage);
            }
        }
        else 
        {
            if($this->flagRender == 0)
            {
                $this->tasksReportsList = $this->tasksReportsInterface->getTasksReports($this->perPage);
            }
            else
            {
                $this->tasksReportsList = $this->tasksReportsInterface->getTasksReportsFilter($this->searchString,$this->technical,$this->client,$this->typeTask,$this->work,$this->ordenation,$this->dateBegin,$this->dateEnd,$this->perPage);
            }
        }

        /***Fim da parte do filtro */

        // return view('tenant.livewire.tasksreports.show', [
        //     'tasksReportsList' => $this->tasksReportsList,
        // ]);

        /***Parte do filtro */

        return view('tenant.livewire.tasksreports.show', [
            'tasksReportsList' => $this->tasksReportsList,
            'members' => $this->members,
            'customers' => $this->customers,
            'services' => $this->service
        ]);

        /***Fim do filtro */
    }

    public function finishTaskReport(int $reportId): void
    {
        $taskReport = $this->tasksReportsInterface->getReport($reportId);
        if(!$taskReport->report || !$taskReport->conclusion) {
            $this->dispatchBrowserEvent('swal', ['title' => __('Task Report'), 'message' => __('The task report is incomplete, please complete the report!'), 'status'=>'error']);
        } else {
            if($taskReport["concluded"] == 0 && $taskReport["infoConcluded"] == "")
            {
                $this->dispatchBrowserEvent('swal', ['title' => __('Task Report'), 'message' => __('The task is not complete and the there is no missing information!'), 'status'=>'error']);
            } else {

                //um dispatch se for sim coloca aqui

                $tasksTimes = TasksTimes::where('task_id',$taskReport->task_id)->get();


                foreach($tasksTimes as $time)
                {
                    if($time->date_end == null)
                    {
                        $this->dispatchBrowserEvent('swal', ['title' => __('Task Report'), 'message' => __('There are still open times!'), 'status'=>'error']);
                        return;
                    }
                }

                $this->dispatchBrowserEvent('swalDispatch', ['title' => __('Finish Task'), 'message' => __('Are you sure you want to finish this task?'), 'status' => 'info', 'function' => 'finishTask', 'parameter' => $reportId, 'parameterSecond' => $taskReport]);


                // $update['reportStatus'] = 2;
                // $update['end_date'] = date('Y-m-d');
                // $update['end_hour'] = date('H:i:s');
                // $this->tasksReportsInterface->updateReport($reportId, $update);
                // event(new DispatchTaskReport(TasksReports::where('id',$taskReport->id)->with('servicesToDo')->with('tasks')->with('tech')->with("taskCustomer")->with('taskLocation')->with('getHoursTask')->first()));

                // $this->dispatchBrowserEvent('swal', ['title' => __('Task Report'), 'message' => __('Task report closed with sucess!'), 'status'=>'info']);
            }
        }
        $usr = User::where('id',Auth::user()->id)->first();

        $message = "fechou uma tarefa";
        
        event(new ChatMessage($usr->name, $message));
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
}
