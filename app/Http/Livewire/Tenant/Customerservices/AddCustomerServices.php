<?php

namespace App\Http\Livewire\Tenant\Customerservices;

use App\Models\Tenant\CustomerLocations;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;

use App\Models\Tenant\CustomerServices;
use App\Models\Tenant\Services;
use App\Models\Tenant\Customers;

class AddCustomerServices extends Component
{
    use WithPagination;

    private object $customerServices;
    public int $perPage;
    public string $searchString = '';

    public object $customerList;
    public object $serviceList;
    public string $selectedCustomer = '';
    public string $selectedService = '';
    public $customer = '';
    public $service = '';
    public string $start_date = '';
    public string $end_date = '';
    public string $new_date = '';
    public string $type = '';

    public string $homePanel = 'show active';
    public string $servicesPanel = '';
    public string $profile = '';
    public $customerLocations;
    public int $selectedLocation = 0;

    protected $listeners = [
        'resetChanges'
     ];

    protected array $rules = [
        'selectedCustomer' => 'required|min:1',
        'selectedService' => 'required|min:1',
    ];

    public function mount($customerList, $serviceList): void
    {
        $this->customerList = $customerList;
        $this->serviceList = $serviceList;

        if (old('selectedService')) {
            $this->selectedService = old('selectedService');
        }

        if (old('selectedCustomer')) {
            $this->selectedCustomer = old('selectedCustomer');
            $this->customerLocations = CustomerLocations::where('customer_id',$this->selectedCustomer)->get();
        }

        if (old('selectedLocation')) {
            $this->selectedLocation = old('selectedLocation');
            $this->customerLocations = CustomerLocations::where('customer_id',$this->selectedCustomer)->get();
        }
       

        if (isset($this->perPage)) {
            session()->put('perPage', $this->perPage);
        } elseif (session('perPage')) {
            $this->perPage = session('perPage');
        } else {
            $this->perPage = 10;
        }
    }

    // public function updatedSelectedCustomer()
    // {
    //     $this->customer = Customers::where('id', $this->selectedCustomer)->first();
    //     $this->customerLocations = CustomerLocations::all();
    //     $this->homePanel = 'show active';
    //     $this->servicesPanel = '';
    //     $this->profile = '';
    //     //$this->dispatchBrowserEvent('contentChanged');
    // }

    // public function updatedCustomer($customerId)
    // {
    //     dd($customerId);
    // }

    public function updateCustomer($customerId)
    {
        $this->customerLocations = CustomerLocations::where('customer_id',$customerId)->get();
    }

    public function updatedSelectedService()
    {
        $this->service = Services::where('id', $this->selectedService)->first();
        $this->homePanel = '';
        $this->servicesPanel = 'show active';
        $this->profile = '';
        $this->dispatchBrowserEvent('contentChanged');
    }

    public function updatedStartDate()
    {
        //$this->dispatchBrowserEvent('contentChanged2');
    }

    public function updatedEndDate()
    {
        //$this->dispatchBrowserEvent('contentChanged2');
    }

    public function cancel() : void
    {
        $this->skipRender();
      
        $this->dispatchBrowserEvent('swal', [
            'title' => __('Customer Location'),
            'message' => __('You will loose all of the changes:'),
            'status' => 'warning',
            'confirm' => 'true',
            'confirmButtonText' => __('Yes, loose changes!'),
            'cancellButtonText' => __('No, keep changes!'),
        ]);
        
    }

    public function resetChanges() : void
    {
        $this->selectedCustomer = '';
        $this->selectedService = '';
    }

    public function save(): Void
    {
        // $validator = Validator::make(
        //     [
        //         'selectedCustomer'  => $this->selectedCustomer,
        //         'selectedLocation' => $this->selectedLocation,
        //         'selectedService' => $this->selectedService,
        //     ],
        //     [
        //         'selectedCustomer'  => 'required|min:1',
        //         'selectedLocation' => 'required|min:1',
        //         'selectedService' => 'required|min:1',
        //     ],
        //     [
        //         'selectedCustomer'  => __('You must select the customer!'),
        //         'selectedService' => __('You must select the customer location!'),
        //         'selectedService' => __('You must select the service!'),
        //     ]
        // );

        if ($validator->fails()) {
            $errorMessage = '';
            foreach($validator->errors()->all() as $message) {
                $errorMessage .= '<p>' . $message . '</p>';
            }
            $this->dispatchBrowserEvent('swal', ['title' => __('Services'), 'message' => $errorMessage, 'status'=>'error']);
        } else {
            $start_date = '1970/01/01';
            $end_date = '1970/01/01';
            if($this->start_date) {
                $start_date = $this->start_date;
            }
            if($this->end_date) {
                $end_date = $this->end_date;
            }
            if($this->new_date) {
                $new_date = $this->new_date;
            }

           
            $CustomerServices->fill([
                'customer_id' => $this->selectedCustomer,
                'service_id' => $this->selectedService,
                'location_id' => $this->selectedLocation,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'new_date' => $new_date,
                'type' => $this->type,
                'last_date' => '1970/01/01'
            ]);
            $CustomerServices->save();

            $this->dispatchBrowserEvent('swal', [
                'title' => __('Customer Services'),
                'message' => __('Service created with success!'),
                'status' => 'info',
            ]);
        }

    }

    public function render(): View
    {
        if(isset($this->searchString) && $this->searchString) {
            $this->customerServices = CustomerServices::where('name', 'like', '%' . $this->searchString . '%')
                ->with('customer')
                ->with('service')
                ->paginate($this->perPage);
        } else {
            $this->customerServices = CustomerServices::with('customer')
                ->with('service')
                ->paginate($this->perPage);
        }

         

        return view('tenant.livewire.customerservices.add', [
            'customerServices' => $this->customerServices,
            'customerList' => $this->customerList,
            'customerLocations' => $this->customerLocations,
            'serviceList' => $this->serviceList,
            'customer' => $this->customer,
            'service' => $this->service,
            'selectedCustomer' => $this->selectedCustomer,
            'selectedService' => $this->selectedService,
            'homePanel' => $this->homePanel,
            'servicesPanel' => $this->servicesPanel,
            'profile' => $this->profile,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'type' => $this->type,

        ]);
    }

}
