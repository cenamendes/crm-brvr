<?php

namespace App\Http\Livewire\Tenant\Tasks;

use Livewire\Component;
use Livewire\Redirector;
use App\Models\Tenant\Tasks;
use App\Models\Tenant\Services;

use App\Models\Tenant\Customers;
use App\Models\Tenant\TeamMember;
use Illuminate\Contracts\View\View;
use SebastianBergmann\Type\VoidType;
use App\Models\Tenant\CustomerServices;
use App\Models\Tenant\CustomerLocations;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\GenerateTaskReference;
use App\Interfaces\Tenant\Tasks\TasksInterface;
use App\Interfaces\Tenant\CustomerServices\CustomerServicesInterface;

class AddTasks extends Component
{
    use GenerateTaskReference;

    public string $homePanel = 'show active';
    public string $servicesPanel = '';
    public string $techPanel = '';
    public string $cancelButton = '';
    public string $actionButton = '';

    public string $selectedCustomer = '';
    public ?object $customerList = NULL;
    public ?object $customer = NULL;
    public string $selectedLocation = '';
    public ?object $customerServicesList = NULL;
    public ?object $customerLocations = NULL;

    public int $numberOfSelectedServices = 0;
    public array $selectedServiceId = [];
    public array $serviceDescription = [];

    public ?string $previewDate = NULL;
    public ?string $previewHour = NULL;
    public ?string $scheduledHour = NULL;
    public ?string $scheduledDate = NULL;

    public ?string $additional_description = NULL;
    public string $selectedTechnician = '';
    public ?object $teamMembers = NULL;
    public ?string $taskAdditionalDescription = '';
    public ?string $taskReference = NULL;
    public int $number = 0;

    private CustomerServicesInterface $customerServicesInterface;
    private TasksInterface $tasksInterface;

    protected $listeners = ['resetChanges' => 'resetChanges'];

    /**
     * Livewire construct function
     *
     * @param TasksInterface $tasksInterface
     * @return Void
     */
    public function boot(CustomerServicesInterface $customerServicesInterface, TasksInterface $tasksInterface): Void
    {
        $this->customerServicesInterface = $customerServicesInterface;
        $this->tasksInterface = $tasksInterface;
    }

    public function mount($customerList): void
    {
        $this->customerList = $customerList;
        $this->cancelButton = '<i class="las la-angle-double-left mr-2"></i>' . __('Back');
        $this->actionButton = __('Create Task');
    }

    public function updatedSelectedCustomer(): Void
    {
        if(!empty($this->customer))
        {
            $this->dispatchBrowserEvent('refreshPage');
        }

        $this->customer = Customers::where('id', $this->selectedCustomer)->with('customerCounty')->with('customerDistrict')->first();
        $this->customerLocations = CustomerLocations::where('customer_id', $this->selectedCustomer)->with('locationCounty')->get();
        $this->dispatchBrowserEvent('contentChanged');
        

        if($this->customer->customerCounty == null)
        {
             $this->dispatchBrowserEvent('swal', ['title' => __('Services'), 'message' => __('You need to select a county for this customer'), 'status'=>'error','function' => 'client']);
             $this->skipRender();
        }
 
    }

    public function updatedSelectedLocation(): Void
    {
        if($this->selectedCustomer == '' || $this->selectedLocation == '') {
            return;
        }
        $this->homePanel = '';
        $this->servicesPanel = 'show active';
        $this->techPanel = '';
        $this->customerServicesList = CustomerServices::where('customer_id', $this->selectedCustomer)
            ->where('location_id', $this->selectedLocation)
            ->with('service')
            ->get();
        $this->teamMembers = TeamMember::get();

        $this->dispatchBrowserEvent('contentChanged');
    }

    public function addServiceForm(): Void
    {
        if(!$this->customerServicesList) {
            $this->homePanel = '';
            $this->servicesPanel = 'show active';
            $this->techPanel = '';
            $this->dispatchBrowserEvent('swal', ['title' => __('Task Services'), 'message' => __('You must select the location!'), 'status'=>'error']);
            return;
        }

        if($this->customerServicesList->count() > $this->numberOfSelectedServices) {
            $this->numberOfSelectedServices++;
            $this->homePanel = '';
            $this->servicesPanel = 'show active';
            $this->techPanel = '';
            $this->dispatchBrowserEvent('newService');
            $this->dispatchBrowserEvent('contentChanged');
        } else {
             $this->dispatchBrowserEvent('swal', ['title' => __('Task Services'), 'message' => __('You cannot add more services to this location!'), 'status'=>'error']);
        }
    }

    public function updatedSelectedServiceId(): Void
    {
        $this->homePanel = '';
        $this->servicesPanel = 'show active';
        $this->techPanel = '';
        $this->changed = true;

        $tempArray = [];
        foreach($this->selectedServiceId as $key => $item) {
            if(!in_array($item, $tempArray)) {
                $tempArray[$key] = $item;
            } elseif (in_array($item, $tempArray) && $item == '') {

            }else {
                $tempArray[$key] = '';
                $this->dispatchBrowserEvent('swal', ['title' => __('Task Services'), 'message' => __('Cannot add the same service!'), 'status'=>'error']);
            }
        }
        $this->selectedServiceId = $tempArray;
        $this->dispatchBrowserEvent('contentChanged');
    }

    public function removeServiceForm($id)
    {
        unset($this->selectedServiceId[$id]);
        unset($this->serviceDescription[$id]);
        $this->numberOfSelectedServices--;
        $this->changed = true;
        $this->dispatchBrowserEvent('contentChanged');
    }

    public function saveTask()
    {
    
        $customer = Customers::where('id', $this->selectedCustomer)->with('customerCounty')->with('customerDistrict')->first();
        
        if($customer != null)
        {
            if($customer->customerCounty == null)
            {
                $this->dispatchBrowserEvent('swal', ['title' => __("Services"), 'message' => __("You need to select a county for this customer"), 'status'=>'error', 'function' => 'client']);
                $this->skipRender();
            }
        }
               

        if(empty($this->serviceDescription))
        {
            $this->dispatchBrowserEvent('swal', ['title' => __("Services"), 'message' => __("You need to select description for all services selected"), 'status'=>'error']);
            return;
        }
        else if(!empty($this->serviceDescription))
        {
            foreach($this->serviceDescription as $service)
            { 
                if($service == '') { 
                    $this->dispatchBrowserEvent('swal', ['title' => __("Services"), 'message' => __("You need to select description for all services selected"), 'status'=>'error']);
                    return;
                }
            }
        }
       
        $validator = Validator::make(
            [
                'selectedCustomer' => $customer->customerCounty,
                'selectedLocation'  => $this->selectedLocation,
                'selectedServiceId' => $this->selectedServiceId,
                'selectedTechnician' => $this->selectedTechnician,
                'serviceDescription' => $this->serviceDescription,
                'previewDate' => $this->previewDate,
            ],
            [
                'selectedLocation'  => 'required|numeric|min:0|not_in:0',
                'selectedServiceId' => function ($attribute, $value, $fail)
                {
                    $found = false;
                    foreach($value as $item) {
                        if($item != '') {
                            $found = true;
                        }
                    }
                   if ($found == false) {
                       $fail('The :attribute must be uppercase.');
                   }
                },
                'serviceDescription' => function ($attribute, $value, $fail)
                {
                    foreach ($value as $item) {
                        if($item == "")
                        {
                            $fail('You should fill a description for the service.');
                        }
                    }
                },
                'selectedTechnician'  => 'required|numeric|min:0|not_in:0',
                'previewDate'  => 'required|string',

            ],
            [
                'selectedCustomer'  => __('You must select the customer location!'),
                'selectedLocation' => __("The selected location field is required."),
                'numberOfSelectedServices' => __('You must select at least a service!'),
                'selectedServiceId' => __('You must select at least a service!'),
                'selectedTechnician' => __('You must select someone to perform this task!'),
                'previewDate' => __('You must select, at least, the preview date!'),
            ]
        );

        if ($validator->fails()) {
            $errorMessage = '';
            foreach($validator->errors()->all() as $message) {
                $errorMessage .= '<p>' . $message . '</p>';
            }
            $this->dispatchBrowserEvent('swal', ['title' => __('Services'), 'message' => $errorMessage, 'status'=>'error']);
            return;
        }
        //$this->dispatchBrowserEvent('loading');

        $latest = Tasks::latest()->first();

        if($latest == null)
        {
            $this->number = 1;
        }
        else if (strpos($latest->created_at, date('Y-m')) === false) {
            $this->number = 1;
        } else {
            // $this->number = $latest('number') + 1;
            $this->number = $latest->number + 1;
        }
        $this->taskReference = $this->taskReference($this->number);

        $this->taskToUpdate = $this->tasksInterface->createTask($this);

        return redirect()->route('tenant.tasks.index')
            ->with('message', __('Task created with success!'))
            ->with('status', 'info');
    }

    public function cancel()
    {
        return $this->askUserLooseChanges();
    }

    public function askUserLooseChanges(): Void
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => __('Task Services'),
            'message' => __('Are you sure? You will loose all the unsaved changes!'),
            'status' => 'question',
            'confirm' => 'true',
            'page' => "add",
            'customer_id' => 1,
            'confirmButtonText' => __('Yes, loose changes!'),
            'cancellButtonText' => __('No, keep changes!'),
        ]);
    }

    public function resetChanges(): Redirector
    {
        //$this->dispatchBrowserEvent('loading');
        session()->put('message', 'Post successfully updated.');
        session()->put('status', 'info');

        return redirect()->route('tenant.tasks.index')
            ->with('message', __('Task updated canceled, all changes where lost!'))
            ->with('status', 'info');
    }

    public function render(): View
    {
        return view('tenant.livewire.tasks.add');
    }

}
