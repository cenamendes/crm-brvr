<?php

namespace App\Http\Livewire\Tenant\Files;

use Livewire\Component;
use App\Events\ChatMessage;
use App\Models\Tenant\Files;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Events\ChatMessage as EventsChatMessage;
use App\Interfaces\Tenant\ChatMessage\ChatInterface;
use App\Interfaces\Tenant\Customers\CustomersInterface;

class Chat extends Component
{
    
    protected $listeners = ["FilesOfThisCustomer" => "FilesOfThisCustomer","chatUpdate","teste"];

    public int $customer_id = 0;

    public ?object $chat;

    protected object $chatRepository;

    public string $usermsg = '';

    public function boot(ChatInterface $interfaceChat)
    {
        $this->chatRepository = $interfaceChat;
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
        $this->chatRepository->messageSend($this->usermsg,Auth::user()->id,$this->customer_id);
        $this->usermsg = '';
        $this->chat = $this->chatRepository->getMessages($this->customer_id);
        event(new ChatMessage());
    }

    /**
     * List informations of customer location
     *
     * @return View
     */
    public function render(): View
    {
               
        return view('tenant.livewire.files.chat', [
         "chat" => $this->chat
        ]);
    }
}
