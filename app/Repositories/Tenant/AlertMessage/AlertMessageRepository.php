<?php

namespace App\Repositories\Tenant\AlertMessage;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Tenant\Files;
use App\Models\Tenant\Customers;
use App\Models\Tenant\TeamMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant\CustomerServices;
use App\Models\Tenant\CustomerLocations;
use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\Tenant\Files\FilesInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\Tenant\Customers\CustomersInterface;
use App\Http\Requests\Tenant\Customers\CustomersFormRequest;
use App\Interfaces\Tenant\AlertMessage\AlertMessageInterface;
use App\Models\Tenant\Notifications;

class AlertMessageRepository implements AlertMessageInterface
{

   public function getNotifications($receiver_user): Collection
    {
      
      $notifications = Notifications::where('receiver_user_id',$receiver_user)->where('read',0)->with('senderUser')->with('receivedUser')->get();
       
      return $notifications;
    }

    public function updateReadState($id_receiver): int
    {

      return DB::transaction(function () use ($id_receiver) {
         
       $notification =  Notifications::where('receiver_user_id',$id_receiver)->update([
            "read" => 1
         ]);

         return $notification;
      });
     
    }

    public function SendNotification($idSender,$customerId,$type): Notifications
    {
        return DB::transaction(function () use ($idSender,$customerId,$type) {
                 
          //Mensagem foi enviada pelo cliente, preciso do membro e do admin
           if(Auth::user()->type_user == 2){
              $customer = Customers::where('id',$customerId)->first();
              $memberOfThisCustomer = TeamMember::where('id',$customer->account_manager)->first();
              $membersToReceive = User::where('id',$memberOfThisCustomer->user_id)->orWhere('type_user',0)->get();
           }
           //Mensagem foi enviada pelo membro, preciso do cliente e do admin
           else if(Auth::user()->type_user == 1){
             $memberThatSend = TeamMember::where('user_id',$idSender)->first();
             $customer = Customers::where('account_manager',$memberThatSend->id)->where('id',$customerId)->first();
             $membersToReceive = User::where('id',$customer->user_id)->orWhere('type_user',0)->get();
           }
           //Mensagem foi enviada pelo admin, preciso do cliente e do membro
           else {
              $customer = Customers::where('id',$customerId)->first();
              $member = TeamMember::where('id',$customer->account_manager)->first();
              $membersToReceive = User::where('id',$customer->user_id)->orWhere('id',$member->user_id)->get();
           }
          
           foreach($membersToReceive as $member)
           {
              $notification = Notifications::create([
                 "sender_user_id" => $idSender,
                 "receiver_user_id" => $member->id,
                 "read" => 0,
                 "type" => $type
              ]);
           }
          
           return $notification;

        });
    }

      

}

