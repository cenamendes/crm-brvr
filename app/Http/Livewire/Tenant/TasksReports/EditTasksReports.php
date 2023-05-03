<?php

namespace App\Http\Livewire\Tenant\TasksReports;

use Livewire\Component;
use Livewire\Redirector;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\Tenant\TasksTimes\TasksTimesInterface;
use App\Interfaces\Tenant\TasksReports\TasksReportsInterface;

class EditTasksReports extends Component
{
    private TasksReportsInterface $tasksReportsInterface;


    public string $searchString = '';
    public string $homePanel = 'active show';
    public string $servicesPanel = '';
    public string $techPanel = '';
    public string $reportPanel = '';
    public string $timesPanel = '';

    public ?object $task = NULL;
    public ?object $taskReport = NULL;

    public int $numberOfSelectedServices = 0;
    public array $selectedServiceId = [];
    public array $serviceDescription = [];

    public int $idReport = 0;
    public string $report = '';
    public string $conclusion = '';
    public string $confidential_information = '';
    public string $infoConcluded = '';
    public int $concluded = 0;

    public ?object $taskTimes =  NULL;

    public int $reportInfo  = 1;

    public ?object $taskReportCollection = NULL;
    public array $arrayReport = [];
    public bool $changed = false;

    protected $listeners = ['resetChanges' => 'resetChanges'];

     /**
     * Livewire construct function
     *
     * @param TasksInterface $tasksInterface
     * @return Void
     */
    public function boot(TasksReportsInterface $tasksReportsInterface): Void
    {
        $this->tasksReportsInterface = $tasksReportsInterface;
    }

    /**
     * Livewire mount properties
     *
     * @return void
     */
    public function mount($taskReportToUpdate, $task): Void
    {
        $this->task == $task;
        $this->taskReport = $taskReportToUpdate;
      
        
        if($taskReportToUpdate->report != null)
        {
            $this->report = $taskReportToUpdate->report;
        }
        if($taskReportToUpdate->conclusion != null)
        {
            $this->conclusion = $taskReportToUpdate->conclusion;
        }
        if($taskReportToUpdate->confidential_information != null)
        {
            $this->confidential_information = $taskReportToUpdate->confidential_information;
        }


        $this->concluded = $taskReportToUpdate->concluded;
        if($taskReportToUpdate->infoConcluded == null)
        {
            $this->infoConcluded = "";
        }
        else {
            $this->infoConcluded = $taskReportToUpdate->infoConcluded;
        }

        $this->cancelButton = __('Back') . '<span class="btn-icon-right"><i class="las la-angle-double-left"></i></span>';
        $this->actionButton = __('Yes, update task report');
        $this->restartCustomerServiceList();

    }

    private function restartCustomerServiceList($tcl = NULL)
    {
        $this->numberOfSelectedServices = $this->task->servicesToDo->count();
        $this->customerServicesList = $this->task->customerServiceList;
        $this->servicesToDo = $this->task->servicesTodo;

        if($tcl != NULL && $tcl->count() == 0) {
            $this->selectedServiceId = [];
            $this->serviceDescription = [];
            $this->servicesToDo = NULL;
            $this->numberOfSelectedServices = 0;
        } else {
            foreach($this->servicesToDo as $item) {
                $this->selectedServiceId[$item->task_service_id] = $item->service->id;
                $this->serviceDescription[] = $item->additional_description;
            }
        }
    }

    /**
     * Changes the changed var to keep track to updated values
     *
     * @return void
     */
    public function updatedReport()
    {
        $this->changed = true;
    }

    /**
     * Changes the changed var to keep track to updated values
     *
     * @return void
     */
    public function updatedConclusion()
    {
        $this->changed = true;
    }

    /**
     * Changes the changed var to keep track to updated values
     *
     * @return void
     */
    public function updatedConfidential_information()
    {
        $this->changed = true;
    }

    /**
     * Saves the task report
     *
     * @return Void
     */
    public function updateTaskReport()
    {
        // $validator = Validator::make(
        //     [
        //         'report'  => $this->report,
        //         'conclusion' => $this->conclusion,
        //         'confidential_information' => $this->confidential_information,
        //     ],
        //     [
        //         'report'  => 'required',
        //         'conclusion'  => 'required',
        //         'confidential_information'  => 'required',
        //     ],
        //     [
        //         'report'  => __('You must insert a report!'),
        //         'conclusion' => __('You must insert a conclusion!'),
        //         'confidential_information' => __('You must insert a confidential information!'),
        //     ]
        // );

        // if ($validator->fails()) {
        //     $errorMessage = '';
        //     foreach($validator->errors()->all() as $message) {
        //         $errorMessage .= '<p>' . $message . '</p>';
        //     }
        //     $this->dispatchBrowserEvent('swal', ['title' => __('Services'), 'message' => $errorMessage, 'status'=>'error']);
        //     return;
        // }
        
        $reportStatus = 0;
        if($this->report || $this->conclusion || $this->confidential_information) {
            $reportStatus = 1;
        }

        if($this->concluded == 1 )
        {
            $this->infoConcluded = "Fechado";
        }

        $save = [
            'report' => $this->report,
            'reportStatus' => $reportStatus,
            'conclusion' => $this->conclusion,
            'confidential_information' => $this->confidential_information,
            'concluded' => $this->concluded,
            'infoConcluded' => $this->infoConcluded
        ];

        $this->reportInfo = $this->tasksReportsInterface->updateReport($this->taskReport->id, $save);

        if($this->reportInfo == 0)
        {
            $this->dispatchBrowserEvent('reportMessage');
        }
        else {
            //$this->dispatchBrowserEvent('loading');
            return redirect()->route('tenant.tasks-reports.index')
                ->with('message', __('Task Report updated with success!'))
                ->with('status', 'info');
        }



    }

    /**
     * Checks if the task was changed and if so asks tbe user if he wants to loose changes or redirect to list of tasks
     *
     * @return null or redirect response
     */
    public function cancel(): NULL|Redirector
    {
        if($this->changed == true )
        {
            $this->askUserLooseChanges();
            return NULL;
        }
        //$this->dispatchBrowserEvent('loading');
        return redirect()->route('tenant.tasks-reports.index');
    }

    /**
     * Ask user if he wants to loose the changes made
     *
     * @return Void
     */
    public function askUserLooseChanges(): Void
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => __('Task Report'),
            'message' => __('Are you sure? You will loose all the unsaved changes!'),
            'status' => 'question',
            'confirm' => 'true',
            'page' => "edit",
            'customer_id' => 1,
            'confirmButtonText' => __('Yes, loose changes!'),
            'cancellButtonText' => __('No, keep changes!'),
        ]);
    }

    /**
     * Confirms the cancelation of the task report
     *
     * @return Redirector
     */
    public function resetChanges(): Redirector
    {
        //$this->dispatchBrowserEvent('loading');
        return redirect()->route('tenant.tasks-reports.index')
            ->with('message', __('Task report updat canceled, all changes where lost!'))
            ->with('status', 'info');
    }

    public function render()
    {

        return view('tenant.livewire.tasksreports.edit');


    }

}
