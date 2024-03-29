<?php

namespace App\Http\Livewire\Tenant\Tasks;

use App\Events\ChatMessage;
use App\Models\User;
use Livewire\Component;

use Livewire\Redirector;

use App\Models\Tenant\Tasks;
use App\Models\Tenant\Services;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Tenant\Customers;
use App\Events\Tasks\TaskCreated;
use App\Models\Tenant\TeamMember;
use App\Events\Tasks\TaskCustomer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use SebastianBergmann\Type\VoidType;
use App\Models\Tenant\CustomerServices;
use Illuminate\Support\Facades\Storage;
use App\Models\Tenant\CustomerLocations;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\GenerateTaskReference;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Interfaces\Tenant\Tasks\TasksInterface;
use App\Interfaces\Tenant\CustomerServices\CustomerServicesInterface;
use App\Models\Tenant\Prioridades;
use App\Models\Tenant\SerieNumbers;

class AddTasks extends Component
{
    use GenerateTaskReference;

    public string $homePanel = 'show active';
    public string $servicesPanel = '';
    public string $equipmentPanel = '';
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
    public ?string $origem_pedido = NULL;
    public ?string $quem_pediu = NULL;
    public ?string $tipo_pedido = NULL;


    public string $selectedTechnician = '';
    public ?object $teamMembers = NULL;
    public ?string $resume = '';
    public ?string $taskAdditionalDescription = '';
    public ?string $taskReference = NULL;
    public int $number = 0;

    public int $alert_email;


    //PARTE DO EQUIPAMENTO
    public ?string $serieNumber = '';
    public ?string $marcaEquipment = '';
    public ?string $modelEquipment = '';

    public ?string $nameEquipment = '';
    public ?string $descriptionEquipment = '';
    
    public ?int $riscado = 0;
    public ?int $partido = 0;
    public ?int $bomestado = 0;
    public ?int $normalestado = 0;

    public ?int $transformador = 0;
    public ?int $mala = 0;
    public ?int $tinteiro = 0;
    public ?int $ac = 0;

    public ?string $descriptionExtra = '';

    public ?string $imagem = '';

    //PARTE DE IR BUSCAR AS CORES

    public ?object $coresObject = NULL;

    public ?int $selectPrioridade;

    /**********/

    private CustomerServicesInterface $customerServicesInterface;
    private TasksInterface $tasksInterface;

    protected $listeners = ['resetChanges' => 'resetChanges', 'responseEmailCustomer' => 'responseEmailCustomer', 'FormAddClient' => 'FormAddClient', 'createCustomerFormResponse' => 'createCustomerFormResponse'];

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

        $this->coresObject = Prioridades::all();
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
        $this->equipmentPanel = '';
        $this->techPanel = '';
        $this->customerServicesList = CustomerServices::where('customer_id', $this->selectedCustomer)
            ->where('location_id', $this->selectedLocation)
            ->with('service')
            ->get();
        $this->teamMembers = TeamMember::where('checkstatus',1)->get();

        $this->dispatchBrowserEvent('contentChanged');
    }

    public function addServiceForm(): Void
    {
        if(!$this->customerServicesList) {
            $this->homePanel = '';
            $this->servicesPanel = 'show active';
            $this->equipmentPanel = '';
            $this->techPanel = '';
            $this->dispatchBrowserEvent('swal', ['title' => __('Task Services'), 'message' => __('You must select the location!'), 'status'=>'error']);
            return;
        }

        if($this->customerServicesList->count() > $this->numberOfSelectedServices) {
            $this->numberOfSelectedServices++;
            $this->homePanel = '';
            $this->servicesPanel = 'show active';
            $this->equipmentPanel = '';
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
        $this->equipmentPanel = '';
        $this->changed = true;

        $tempArray = [];
        foreach($this->selectedServiceId as $key => $item) {
            if(!in_array($item, $tempArray)) {
                $tempArray[$key] = $item;
            } elseif (in_array($item, $tempArray) && $item == '') {

            }else {
                $tempArray[$key] = '';
                $this->dispatchBrowserEvent('swal', ['title' => __('Task Services'), 'message' => __('Cannot add the same service!'), 'status'=>'error', 'function' => 'same']);
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

    public function FormAddClient()
    {
        $message = "";

        $message = "<div class='swalBox'>";
            $message .= "<div class='row mt-4' style='justify-content:center;'>";
                $message .= "<section class='col-xl-12'>";
                  $message .= "<label>Nome Cliente</label>";
                  $message .= "<input type='text' name='customer_name' id='customer_name' class='form-control'>";
                $message .= "</section>";
                $message .= "<section class='col-xl-12'>";
                    $message .= "<label>NIF</label>";
                    $message .= "<input type='text' name='nif' id='nif' class='form-control'>";
                $message .= "</section>";
                $message .= "<section class='col-xl-12'>";
                    $message .= "<label>Contacto</label>";
                    $message .= "<input type='text' name='contact' id='contact' class='form-control'>";
                $message .= "</section>";
                $message .= "<section class='col-xl-12' style='margin-bottom:20px;'>";
                    $message .= "<label>Email</label>";
                    $message .= "<input type='text' name='email' id='email' class='form-control'>";
                $message .= "</section>";
                $message .= "<button type='button' id='buttonresponseCustomer' data-anwser='ok' class='btn btn-primary'>Enviar</button>";
                $message .= "&nbsp;<button type='button' class='btn btn-secondary' id='buttonresponseCustomer' data-anwser='close'>Fechar</button>";
            $message .= "</div>";
        $message .= "</div>";

        $this->dispatchBrowserEvent('createCustomer', ['title' => __('Formulário Cliente'), 'message' => $message, 'status' => 'info']);
    }

    public function createCustomerFormResponse($name,$nif,$contact,$email)
    {
       $allLower = strtolower($name);
       
       $slug = str_replace(" ","-",$allLower);

       $validator = Validator::make(
            [
                'name' => $name,
                'nif'  => $nif,
                'contact' => $contact,
                'email' => $email
            ],
            [
                'name'  => 'required',
                'nif'  => 'required|min:9|max:9',
                'contact'  => 'required',
                'email' => 'required'
            ],
            [
                'name'  => "Tem de inserir um nome!",
                'nif' => "Tem de inserir um nif com 9 digitos!",
                'contact' => "Tem de inserir um contacto!",
                'email' => "Tem de inserir um email!"
            ]
        );

        if ($validator->fails()) {
            $errorMessage = '';
            foreach($validator->errors()->all() as $message) {
                $errorMessage .= '<p>' . $message . '</p>';
            }
            $this->dispatchBrowserEvent('swal', ['title' => __('Inserir Cliente'), 'message' => $errorMessage, 'status'=>'error', 'whatfunction'=>"add"]);
            return;
        }

        $checkBefore = Customers::where('vat', $nif)->first();

        if($checkBefore != null)
        {
            $this->dispatchBrowserEvent('swal', ['title' => __('Inserir Cliente'), 'message' => "Esse cliente já se encontra registado", 'status'=>'error', 'whatfunction'=>"add"]);
            return;
        }

        $response = Customers::Create([
            "name" => $name,
            "slug" => $slug,
            "short_name" => $slug,
            "username" => $email,
            "vat" => $nif,
            "contact" => $contact,
            "email" => $email,
            "address" => "Rua de Regufe, 33",
            "zipcode" => "4480-246",
            "district" => '13',
            "county" => '16',
            "account_manager" => '9'
        ]);

        $location = CustomerLocations::Create([
            "description" => "Sede",
            "customer_id" => $response->id,
            "main" => "1",
            "address" => "Rua de Regufe, 33",
            "zipcode" => "4480-246",
            "contact" => $contact,
            "district_id" => '13',
            "county_id" => '16',
            "manager_name" => "Vitor Oliveira",
            "manager_contact" => $contact
        ]);

        CustomerServices::Create([
            "customer_id" => $response->id,
            "service_id" => "4",
            "location_id" => $location->id,
            "start_date" => date('Y-m-d')
        ]);



        $this->dispatchBrowserEvent('swal', ['title' => "Cliente", 'message' => 'Cliente criado com sucesso!', 'status'=>'sucess', 'whatfunction'=>"finishInsert"]);

    }

    public function searchSerieNumber()
    {
        $response = $this->tasksInterface->searchSerialNumber($this->serieNumber);

        if(!isset($response[0]->marca)){
            $this->marcaEquipment = '';
        }
        else {
            $this->marcaEquipment = $response[0]->marca;
        }

        if(!isset($response[0]->modelo)){
            $this->modelEquipment = '';
        }
        else {
            $this->modelEquipment = $response[0]->modelo;
        }

        if($this->serieNumber == "")
        {
            $this->marcaEquipment = '';
            $this->modelEquipment = '';
        }

        
        $this->homePanel = '';
        $this->servicesPanel = '';
        $this->equipmentPanel = 'show active';
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
            $this->dispatchBrowserEvent('swal', ['title' => __("Services"), 'message' => __("You need to select description for all services selected"), 'status'=>'error', 'whatfunction' => 'servicesMissing']);
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
                'origem_pedido' => $this->origem_pedido,
                'quem_pediu' => $this->quem_pediu,
                'tipo_pedido' => $this->tipo_pedido
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

                'origem_pedido'  => 'required|string',
                'quem_pediu'  => 'required|string',
                'tipo_pedido'  => 'required|string',

            ],
            [
                'selectedCustomer'  => __('You must select the customer location!'),
                'selectedLocation' => __("The selected location field is required."),
                'numberOfSelectedServices' => __('You must select at least a service!'),
                'selectedServiceId' => __('You must select at least a service!'),
                'selectedTechnician' => __('You must select someone to perform this task!'),
                'previewDate' => __('You must select, at least, the preview date!'),

                'origem_pedido' => __('You must select, a request origin!'),
                'quem_pediu' => __('You must select, who asked!'),
                'tipo_pedido' => __('You must select, a type of request!'),
            ]
        );

        if ($validator->fails()) {
            $errorMessage = '';
            foreach($validator->errors()->all() as $message) {
                $errorMessage .= '<p>' . $message . '</p>';
            }
            $this->dispatchBrowserEvent('swal', ['title' => __('Services'), 'message' => $errorMessage, 'status'=>'error', 'whatfunction'=>"add"]);
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

        //Pesquisa se esse numero de serie existe na base de dados

        $serieNumberSearch = SerieNumbers::where('nr_serie',$this->serieNumber)->first();

        if($serieNumberSearch == null)
        {
            SerieNumbers::Create([
                "nr_serie" => $this->serieNumber,
                "marca" => $this->marcaEquipment,
                "modelo" => $this->modelEquipment
            ]);
        }


        $this->taskReference = $this->taskReference($this->number);

      

        //fazer a criacao do PDF
        if($this->riscado != 0 || $this->partido != 0 || $this->bomestado != 0 || $this->normalestado != 0 || $this->transformador != 0 || $this->mala != 0 || $this->tinteiro != 0 || $this->ac != 0)
        {
            $qrcode = base64_encode(QrCode::size(150)->generate('https://hihello.me/pt/p/adc8b89e-a3de-4033-beeb-43384aafa1c3?f=email'));
       
            $customPaper = array(0, 0, 400.00, 216.00);
            $pdf = PDF::loadView('tenant.livewire.tasks.impressaopdf',["impressao" => $this, "qrcode" => $qrcode])->setPaper($customPaper);
    
            if(!Storage::exists(tenant('id') . '/app/impressoes'))
            {
                File::makeDirectory(storage_path('app/impressoes'), 0755, true, true);
            }
    
            $content = $pdf->download()->getOriginalContent();

            $this->imagem = 'impressao'.$this->taskReference.'.pdf';

    
            Storage::put(tenant('id') . '/app/impressoes/impressao'.$this->taskReference.'.pdf',$content);
        }
       
        $this->taskToUpdate = $this->tasksInterface->createTask($this);

        /********************** */
              
        $techUser = TeamMember::where('id',$this->taskToUpdate->tech_id)->first();

        $user = User::where('id',$techUser->user_id)->first();

        $task = Tasks::where('id',$this->taskToUpdate->id)->with('servicesToDo')->first();


        // if(Auth::user()->id != $user->id && $user != null)
        // {
        //     event(new TaskCreated($task));
        // }

        $message = "";

        $message = "<div class='swalBox'>";
        $message .= "<div class='row mt-4' style='justify-content:center;'>";
        $message .= "<button type='button' id='buttonresponse' data-anwser='ok' class='btn btn-primary'>Enviar</button>";
        $message .= "&nbsp;<button type='button' class='btn btn-secondary' id='buttonresponse' data-anwser='close'>Fechar</button>";
        $message .= "</div>";
        $message .= "</div>";

        $this->dispatchBrowserEvent('SendEmailTech', ['title' => __('Quer enviar email para o cliente?'), 'message' => $message, 'status' => 'info', 'parameter_function' => $task]);

        if($this->riscado != 0 || $this->partido != 0 || $this->bomestado != 0 || $this->normalestado != 0 || $this->transformador != 0 || $this->mala != 0 || $this->tinteiro != 0 || $this->ac != 0)
        {
            return response()->streamDownload(function () use($pdf) {
                echo  $pdf->stream();
            }, 'etiqueta.pdf');
        }

        
        $usr = User::where('id',Auth::user()->id)->first();

        if(Auth::user()->id == $techUser->user_id){
            $message = "adicionou uma tarefa nova";
        } else {
            $message = "adicionou uma tarefa nova para ".$user->name."";
        }

        
        event(new ChatMessage($usr->name, $message));
    
    }

    public function responseEmailCustomer($email,$response,$responseEmailCustomer)
    {

        if($response == "ok")
        {
            event(new TaskCustomer($responseEmailCustomer));
        }

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
