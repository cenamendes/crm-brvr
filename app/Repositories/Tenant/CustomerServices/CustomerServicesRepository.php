<?php

namespace App\Repositories\Tenant\CustomerServices;

use App\Http\Livewire\Tenant\Common\Location;
use App\Models\Tenant\Customers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\CustomerServices;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\Tenant\Customers\CustomersFormRequest;
use App\Interfaces\Tenant\CustomerServices\CustomerServicesInterface;
use App\Http\Requests\Tenant\CustomersServices\CustomersServicesFormRequest;

class CustomerServicesRepository implements CustomerServicesInterface
{
    public function getAllCustomerServices($perPage): LengthAwarePaginator
    {
        $customerServices = CustomerServices::with('customer')
                            ->with('service')
                            ->paginate($perPage);

        return $customerServices;
    }

    public function getSearchedCustomerService($searchString,$perPage): LengthAwarePaginator
    {
        $customerServices = CustomerServices::whereHas('customer', function ($query) use($searchString) {
            $query->where('name', 'like', '%' . $searchString . '%');
        })
        ->with('service')
        ->paginate($perPage);

        return $customerServices;
    }

    public function getSearchedCustomerServiceWithFilterCostumer($customerId,$searchString,$perPage): LengthAwarePaginator
    {
        $customerServices = CustomerServices::where('customer_id', $customerId)->whereHas('customer', function ($query) use($searchString) {
            $query->where('name', 'like', '%' . $searchString . '%');
        })
        ->with('service')
        ->paginate($perPage);

        return $customerServices;
    }

    public function add(CustomersServicesFormRequest $request): CustomerServices
    {
        return DB::transaction(function () use ($request) {
            $customerService = CustomerServices::create([
                'customer_id' => $request->selectedCustomer,
                'service_id' => $request->selectedService,
                'location_id' => $request->selectedLocation,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'type' => $request->type,
                'alert' => $request->alert
            ]);


            return $customerService;
        });
    }

    public function update(int $customerService, CustomersServicesFormRequest $request): int
    {
        return DB::transaction(function () use ($customerService,$request) {
            $customerServiceRequest[]= $request->all();

            $arrayCustomerServices = [];

            $serviceRequest = [];
            foreach($customerServiceRequest as $req)
            {
                if($req["selectedService"] != "")
                {
                    $serviceRequest["service_id"] = $req["selectedService"];
                    array_push($arrayCustomerServices, $serviceRequest);
                }
                
                if($req["start_date"] != "")
                {
                    $serviceRequest["start_date"] = $req["start_date"];
                    array_push($arrayCustomerServices, $serviceRequest);
                }
                if($req["end_date"] != "")
                {
                    $serviceRequest["end_date"] = $req["end_date"];
                    array_push($arrayCustomerServices, $serviceRequest);
                }
                if($req["type"] != "")
                {
                    $serviceRequest["type"] = $req["type"];
                    array_push($arrayCustomerServices, $serviceRequest);
                }
                if($req["selectedLocation"] != "")
                {
                    $serviceRequest["location_id"] = $req["selectedLocation"];
                    array_push($arrayCustomerServices, $serviceRequest);
                }
                if($req["alert"] != "")
                {
                    $serviceRequest["alert"] = $req["alert"];
                    array_push($arrayCustomerServices,$serviceRequest);
                }

            }


            $customerService = CustomerServices::where('id',$customerService)->update(array_pop($arrayCustomerServices));
            return $customerService;
        });


    }

    public function destroy($customerService): int
    {
        return DB::transaction(function () use ($customerService) {
            $customerService = CustomerServices::where('id',$customerService)->delete();
            return $customerService;
        });

    }

    public function getCustomerServices(Customers $customer, string $location)
    {
        return CustomerServices::where('customer_id', $customer->id)
            ->where('location_id', $location)
            ->with('service')
            ->get();
    }

    public function getNotificationTimes(): Array
    {
        $notificationInfo = [];
        $servicesNotifications = CustomerServices::where('end_date', '<=',date('Y-m-d'))->where('treated',1)->with('service')->with('customer')->with('customerLocation')->get();

        foreach($servicesNotifications as $count => $notification)
        {
            $date_of_notification = date('Y-m-d', strtotime('-'.$notification->alert.' day',strtotime($notification->end_date)));
            $notificationInfo[$count] = ["customerServicesId" => $notification->id, "service" => $notification->service->name, "date_final_service" => $notification->end_date, "customer" => $notification->customer->short_name, "customer_county" => $notification->customerLocation->locationDistrict->name, "notification" => $date_of_notification, "tratada" => $notification->treated];
        }

        return $notificationInfo;
    }

    public function changeTreatedStatus($idCustomerService): void
    {
        $customerService = CustomerServices::where('id',$idCustomerService)->update(["treated" => "2"]);

    }
    

}
