<?php

namespace App\Interfaces\Tenant\ChatMessage;

use App\Models\Tenant\Files;
use App\Models\Tenant\ChatMessage;
use Illuminate\Support\Collection;

interface ChatInterface
{
    public function getMessages($id): Collection;

    public function messageSend($message,$user_id,$customer_id): ChatMessage;
   

}
