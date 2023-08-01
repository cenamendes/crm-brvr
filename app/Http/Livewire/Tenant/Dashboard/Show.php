<?php

namespace App\Http\Livewire\Tenant\Dashboard;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Tenant\Tasks;
use App\Models\Tenant\Customers;
use App\Models\Tenant\TasksTimes;
use App\Models\Tenant\TasksReports;
use App\Interfaces\Tenant\Tasks\TasksInterface;
use App\Interfaces\Tenant\TeamMember\TeamMemberInterface;
use App\Interfaces\Tenant\CustomerServices\CustomerServicesInterface;
use App\Interfaces\Tenant\CustomerNotification\CustomerNotificationInterface;

class Show extends Component
{
    
    protected $listeners = ["CalendarNextChanges" => 'CalendarNextChanges', "CalendarPreviousChanges" => 'CalendarPreviousChanges', "checkReport" => 'checkReport'];
    public string $month = '';

    public string $nextMonth = "";
    public string $nextYear = "";

    public int $avancoMes = 0;

    private ?object $teamMembersResponse = NULL;
    private ?object $tasks = NULL;

    private ?array $openTimes = [];

    private ?array $servicesNotifications = [];

    protected TeamMemberInterface $TeamMember;
    protected CustomerNotificationInterface $customerNotification;
    protected TasksInterface $taskInterface;

    public function boot(TeamMemberInterface $members, CustomerNotificationInterface $customerNotification, TasksInterface $taskInterface)
    {
        $this->TeamMember = $members;
        $this->customerNotification = $customerNotification;
        $this->taskInterface = $taskInterface;
    }
    public function mount()
    {
        $this->tasks = $this->taskInterface->taskCalendar();
        $this->servicesNotifications = $this->customerNotification->getNotificationTimes();

        //PARTE EM TEMPO REAL
       
        $users = User::all();

        $taskTimes = TasksTimes::with('service')->with('task')->orderBy('id','asc')->where('date_end',null)->get();

        foreach($users as $i => $user)
        {
            foreach($taskTimes as $count => $time)
            {
                if($user->id == $time->tech_id)
                {
                    $serviceName = $time->service->name;

                    $taskReference = $time->task->reference;

                    $customer_id = $time->task->customer_id;

                    $customer_name = Customers::where('id',$customer_id)->first();

                    $timeReport = TasksReports::where('task_id',$time->task_id)->first();

                    $arrayTimes[$user->name] = [
                        "service" => $serviceName,
                        "reference" => $taskReference,
                        "customer" => $customer_name->name,
                        "date_begin" => $time->date_begin,
                        "hour_begin" => $time->hour_begin,
                        "task_id" => $timeReport->id,
                        "tech" => $time->tech_id
                    ];
                }
            }
        }

        $this->openTimes = $arrayTimes;

    }


    public function CalendarPreviousChanges($date,$state)
    {
        if($state == "Mês")
        {
            // $this->avancoMes--;
            // $month = date('m',strtotime("+".$this->avancoMes." month",strtotime($date)));
            // $year = date('Y', strtotime("+".$this->avancoMes." month",strtotime($date)));

            // $this->nextMonth = $month;
            // $this->nextYear = $year;

            // $this->tasks = $this->taskInterface->taskCalendarMonthChange($month,$year);
            // $this->servicesNotifications = $this->customerNotification->getNotificationTimes();

            // $this->dispatchBrowserEvent("calendar",["calendarResult" => "".$this->nextYear."-".$this->nextMonth."-01T10:00:00"]);
            $this->skipRender();
        }
        else {
            $this->skipRender();
        }


       
    }

    public function CalendarNextChanges($date,$state)
    {
        if($state == "Mês")
        {
            // $this->avancoMes++;
            // $month = date('m', strtotime("+".$this->avancoMes." month",strtotime($date)));
            // $year = date('Y', strtotime("+".$this->avancoMes." month",strtotime($date)));

            // $this->nextMonth = $month;
            // $this->nextYear = $year;

            // $this->tasks = $this->taskInterface->taskCalendarMonthChange($month,$year);
            // $this->servicesNotifications = $this->customerNotification->getNotificationTimes();
                    
            // $this->dispatchBrowserEvent("calendar",["calendarResult" => "".$this->nextYear."-".$this->nextMonth."-01T10:00:00"]);
            $this->skipRender();
        }
        else {
            $this->skipRender();
        }
    }

    public function checkReport($task)
    {
        $report = TasksReports::where('task_id',$task)->first();

        if($report != null){
            $this->dispatchBrowserEvent("responseReport",["response" => "existe", "value" => $report->id]);
        }
        else {
            $this->dispatchBrowserEvent("responseReport",["response" => "naoexiste", "value" => $task]);
        }

        $this->skipRender();
    }

    public function treated($id)
    {
        $this->customerNotification->changeTreatedStatus($id);
        $this->servicesNotifications = $this->customerNotification->getNotificationTimes();
        $this->teamMembersResponse = $this->TeamMember->getAllTeamMembers(0);

        $this->tasks = $this->taskInterface->taskCalendar();
    }

    public function render()
    {
        $perPage = 0;
        $this->teamMembersResponse = $this->TeamMember->getAllTeamMembers($perPage);

        return view('tenant.livewire.dashboard.show',['teamMembers' => $this->teamMembersResponse,'tasks' => $this->tasks ,'servicesNotifications' => $this->servicesNotifications, 'openTimes' => $this->openTimes]);
    }
}
