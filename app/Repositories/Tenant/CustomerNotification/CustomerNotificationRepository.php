<?php

namespace App\Repositories\Tenant\CustomerNotification;

use App\Models\Tenant\CustomerNotifications;
use App\Interfaces\Tenant\CustomerNotification\CustomerNotificationInterface;

class CustomerNotificationRepository implements CustomerNotificationInterface
{
    
    public function getNotificationTimes(): Array
    {
        $notificationInfo = [];
        $servicesNotifications = CustomerNotifications::where('treated',1)->with('service')->with('customer')->with('customerLocation')->get();

        foreach($servicesNotifications as $count => $notification)
        {
            $notificationInfo[$count] = ["customerServicesId" => $notification->id, "service" => $notification->service->name, "date_final_service" => $notification->end_service_date, "customer" => $notification->customer->short_name, "customer_county" => $notification->customerLocation->description, "notification" => $notification->notification_day, "tratada" => $notification->treated];
        }

        return $notificationInfo;
    }

    public function changeTreatedStatus($idCustomerService): void
    {
        CustomerNotifications::where('id',$idCustomerService)->update(["treated" => "2"]);
    }
    

}
