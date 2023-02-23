<?php

namespace App\Http\Livewire\Tenant\Files;

use App\Models\User;
use Livewire\Component;
use App\Events\ChatMessage;
use App\Events\LoginStatus;
use App\Models\Tenant\Files;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Events\ChatMessageAdmin;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Events\ChatMessage as EventsChatMessage;
use App\Interfaces\Tenant\ChatMessage\ChatInterface;
use App\Interfaces\Tenant\Customers\CustomersInterface;
use App\Interfaces\Tenant\AlertMessage\AlertMessageInterface;

class Chat extends Component
{
    
    protected $listeners = ["FilesOfThisCustomer" => "FilesOfThisCustomer","chatUpdate","teste","loginStatus"];

    use WithFileUploads;

    public int $customer_id = 0;

    public ?object $chat;

    protected object $chatRepository;

    protected object $alertRepository;

    public string $usermsg = '';

    public $fileUploaded;

    public function boot(ChatInterface $interfaceChat,AlertMessageInterface $interfaceAlert)
    {
        $this->chatRepository = $interfaceChat;
        $this->alertRepository = $interfaceAlert;
    }

    public function mount($customer): void
    {
        $this->customer_id = $customer;
        $this->chat = $this->chatRepository->getMessages($this->customer_id);
             
    }

    public function teste()
    {
        $this->dispatchBrowserEvent("refreshChatPosition"); 
    }

    public function chatUpdate()
    {
        $this->chat = $this->chatRepository->getMessages($this->customer_id);
        $this->dispatchBrowserEvent("refreshChatPosition");   
    }

    public function FilesOfThisCustomer($id)
    {
        $this->customer_id = $id; 
        $this->chat = $this->chatRepository->getMessages($this->customer_id);     
    }

    public function SendMessage()
    {
        if($this->usermsg != "")
        {
            $this->chatRepository->messageSend($this->usermsg,Auth::user()->id,$this->customer_id);
            $this->usermsg = '';
            $this->chat = $this->chatRepository->getMessages($this->customer_id);
            $this->alertRepository->SendNotification(Auth::user()->id,$this->customer_id,"message");
            event(new ChatMessage());
        }
    }

    public function updatedFileUploaded()
    {
        $this->dispatchBrowserEvent("fileCome");
        //teria que mandar para a input
    }

    /**
     * List informations of customer location
     *
     * @return View
     */
    public function render(): View
    {
               
        return view('tenant.livewire.files.chat', [
            "chat" => $this->chat,
            "customer" => $this->customer_id
        ]);
    }
}
