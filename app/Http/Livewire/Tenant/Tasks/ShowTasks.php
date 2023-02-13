<?php

namespace App\Http\Livewire\Tenant\Tasks;

use Livewire\Component;
use App\Models\Tenant\Tasks;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use App\Events\Tasks\DispatchTasksToUser;
use App\Interfaces\Tenant\Tasks\TasksInterface;
use App\Interfaces\Tenant\TasksReports\TasksReportsInterface;

class ShowTasks extends Component
{
    use WithPagination;

    public int $perPage;
    public string $searchString = '';
    private ?object $tasksList = NULL;
    private ?object $counties = NULL;

    protected $listeners = ['dispatchTask' => 'dispatchTask'];

    private TasksInterface $tasksInterface;
    private TasksReportsInterface $tasksReportsInterface;
    private ?object $task = NULL;
    public int $taskId = 0;

    /**
     * Livewire construct function
     *
     * @param TasksInterface $tasksInterface
     * @return Void
     */
    public function boot(TasksInterface $tasksInterface, TasksReportsInterface $tasksReportsInterface): Void
    {
        $this->tasksInterface = $tasksInterface;
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
        $this->tasksList = $this->tasksInterface->getTasks($this->perPage);
    }

    /**
     * Ask if the customer wants to schedule the task
     *
     * @return Void
     */
    public function askToSchedule($id): Void
    {
        $this->taskId = $id;
        $this->dispatchBrowserEvent('swalModalQuestion', [
            'title' => __('Schedule Task'),
            'message' => __('Do you want to schedule the task?'),
            'status' => 'question',
            'confirm' => 'true',
            'confirmButtonText' => __('Yes, schedule the task!'),
            'cancellButtonText' => __('No, do not schedule the task!'),
            'function' => "dispatchTask"
        ]);
        $this->skipRender();
    }

    /**
     * Schedule the task
     *
     * @return Void
     */
    public function dispatchTask(): Void
    {
        $this->task = $this->tasksInterface->getTaskById($this->taskId);
        $this->task->status = 1;
        $error = true;
        if (!$this->task->scheduled_date) {
            if ($this->task->preview_date > date('Y-m-d')) {
                $this->task->scheduled_date = $this->task->preview_date;
                $this->task->scheduled_hour = $this->task->preview_hour;
                $error = false;
            } else if ($this->task->preview_date == date('Y-m-d') && ($this->task->preview_hour > date('H:i:s'))) {
                $this->task->scheduled_date = $this->task->preview_date;
                $this->task->scheduled_hour = $this->task->preview_hour;
                $error = false;
            } else {
                $this->dispatchBrowserEvent('swalModalQuestion', ['title' => __('Tasks'), 'message' => __('You must set the schedule date and time!'), 'status'=>'error']);
            }
        } else if ($this->task->scheduled_date) {
            if ($this->task->scheduled_date < date('Y-m-d')) {
                $this->dispatchBrowserEvent('swalModalQuestion', ['title' => __('Tasks'), 'message' => __('Cannot assign a task in the past!'), 'status'=>'error']);
            } else {
                $error = false;
            }
        }

        $updateTask = false;

        $tasksReports = $this->tasksReportsInterface->getReportByTaskId($this->task->id);

        if($tasksReports !== NULL) {
            if($tasksReports->reportStatus > 0) {
                $this->dispatchBrowserEvent('swalModalQuestion', ['title' => __('Tasks'), 'message' => __('This task already has a report!'), 'status'=>'error']);
                $error = true;
            } else {
                $updateTask = true;
            }
        }

        if ( $error == false)
        {
            if($updateTask === false) {
                if($this->tasksInterface->dispatchTask($this->task)) {
                    event(new DispatchTasksToUser(Tasks::with('servicesToDo')->with('tech')->with("taskCustomer")->with('taskLocation')->where('id',$this->task->id)->first()));
                    $this->dispatchBrowserEvent('swalModalQuestion', ['title' => __('Tasks'), 'message' => __('Task scheduled with success!'), 'status'=>'info']);
                } else {
                    $this->dispatchBrowserEvent('swalModalQuestion', ['title' => __('Tasks'), 'message' => __('Error while scheduling task!'), 'status'=>'error']);
                }
            } else {
               if($this->tasksReportsInterface->updateTaskReport($this->task)) {
                event(new DispatchTasksToUser(Tasks::with('servicesToDo')->with('tech')->with("taskCustomer")->with('taskLocation')->where('id',$this->task->id)->first()));
                $this->dispatchBrowserEvent('swalModalQuestion', ['title' => __('Tasks'), 'message' => __('Task scheduled with success!'), 'status'=>'info']);
                } else {
                    $this->dispatchBrowserEvent('swalModalQuestion', ['title' => __('Tasks'), 'message' => __('Error while scheduling task!'), 'status'=>'error']);
                }
            }


        }
        $this->tasksList = $this->tasksInterface->getTasks($this->perPage);
    }

    /**
     * Change number of records to display
     *
     * @return void
     */
    public function updatedPerPage(): void
    {
        $this->resetPage();
        session()->put('perPage', $this->perPage);
        $this->tasksList = $this->tasksInterface->getTasks($this->perPage);
    }

    /**
     * Do a search base on
     *
     * @return void
     */
    public function updatedSearchString(): void
    {
        $this->resetPage();
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
     * Livewire render list tasks view
     *
     * @return View
     */
    public function render(): View
    {
        if($this->searchString != null)
        {
            $this->tasksList = $this->tasksInterface->getTaskSearch($this->searchString,$this->perPage);
        }
        else
        {
            $this->tasksList = $this->tasksInterface->getTasks($this->perPage);
        }

        return view('tenant.livewire.tasks.show', [
            'tasksList' => $this->tasksList,
        ]);
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
