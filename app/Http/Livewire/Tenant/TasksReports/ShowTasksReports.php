<?php

namespace App\Http\Livewire\Tenant\TasksReports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant\TasksReports;
use App\Events\Tasks\DispatchTaskReport;
use App\Interfaces\Tenant\TasksReports\TasksReportsInterface;

class ShowTasksReports extends Component
{

    use WithPagination;

    private TasksReportsInterface $tasksReportsInterface;
    private ?object $tasksReportsList = NULL;
    public string $searchString = '';

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
    public function mount(): Void
    {
        $this->initProperties();

    }

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
        if(isset($this->searchString) && $this->searchString) {
            $this->tasksReportsList = $this->tasksReportsInterface->getTaskReport($this->searchString,$this->perPage);
        } else {
            $this->tasksReportsList = $this->tasksReportsInterface->getTasksReports($this->perPage);
        }

        return view('tenant.livewire.tasksreports.show', [
            'tasksReportsList' => $this->tasksReportsList,
        ]);
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
                $update['reportStatus'] = 2;
                $update['end_date'] = date('Y-m-d');
                $update['end_hour'] = date('H:i:s');
                $this->tasksReportsInterface->updateReport($reportId, $update);
                event(new DispatchTaskReport(TasksReports::where('id',$taskReport->id)->with('servicesToDo')->with('tech')->with("taskCustomer")->with('taskLocation')->with('getHoursTask')->first()));

                $this->dispatchBrowserEvent('swal', ['title' => __('Task Report'), 'message' => __('Task report closed with sucess!'), 'status'=>'info']);
            }
        }
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
