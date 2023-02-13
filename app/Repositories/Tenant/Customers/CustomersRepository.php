<?php

namespace App\Repositories\Tenant\Customers;

use App\Models\Tenant\Customers;
use App\Models\Tenant\TeamMember;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant\CustomerServices;
use App\Models\Tenant\ContactsCustomers;
use App\Models\Tenant\CustomerLocations;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Interfaces\Tenant\Customers\CustomersInterface;
use App\Http\Requests\Tenant\Customers\CustomersFormRequest;
use App\Http\Requests\Tenant\CustomerContacts\CustomerContactsFormRequest;
use Illuminate\Database\Eloquent\Collection;

class CustomersRepository implements CustomersInterface
{
    public function getAllCustomers($perPage): LengthAwarePaginator
    {
        $customers = Customers::with('customerDistrict')->paginate($perPage);
        return $customers;
    }

    public function getCustomersAnalysis(): Collection
    {
        $customers = Customers::all();
        return $customers;
    }

    public function getSearchedCustomer($searchString, $perPage): LengthAwarePaginator
    {
        $customers = Customers::where('name', 'like', '%' . $searchString . '%')->paginate($perPage);
        return $customers;
    }

    public function getLocationsFromCustomer($customer_id,$searchString, $perPage): LengthAwarePaginator
    {
       $customer = CustomerLocations::where('customer_id', $customer_id)->where('description', 'like', '%' . $searchString . '%')->paginate($perPage);
       return $customer;
    }

    public function add(CustomersFormRequest $request): Customers
    {
        return DB::transaction(function () use ($request) {
            $Customer = Customers::create([
                'name' => $request->name,
                'short_name' => $request->short_name,
                'vat' => $request->vat,
                'contact' => $request->contact,
                'email' => $request->email,
                'address' => $request->address,
                'district' => $request->district,
                'county' => $request->county,
                'zipcode' => $request->zipcode,
                'zone' => '1',
                'account_manager' => $request->account_manager,
            ]);

            $memberInfo = TeamMember::where('id',$request->account_manager)->first();
            $manager_name = $memberInfo->name;
            $manager_contact = $memberInfo->mobile_phone;

            CustomerLocations::create([
                'description' => __('Main address'),
                'customer_id' => $Customer->id,
                'main' => 1,
                'address' => $request->address,
                'zipcode' => $request->zipcode,
                'district_id' => $request->district,
                'county_id' => $request->county,
                'contact' => $request->contact,
                'manager_name' => $manager_name,
                'manager_contact' => $manager_contact,
            ]);
            return $Customer;
        });
    }

    public function update(Customers $customer,CustomersFormRequest $request): Customers
    {
        return DB::transaction(function () use ($customer,$request) {

            $customer->fill($request->all());
            $customer->save();

            $customerLocationRequest[]= $request->all();

            $arrayCustomerLocation = [];

            $newCompete = [];
            foreach($customerLocationRequest as $req)
            {
                if($req["address"] != "")
                {
                    $newCompete["address"] = $req["address"];
                    array_push($arrayCustomerLocation, $newCompete);
                }
                if($req["zipcode"] != "")
                {
                    $newCompete["zipcode"] = $req["zipcode"];
                    array_push($arrayCustomerLocation, $newCompete);
                }
                if($req["contact"] != "")
                {
                    $newCompete["contact"] = $req["contact"];
                    array_push($arrayCustomerLocation, $newCompete);
                }
                if($req["district"] != "")
                {
                    $newCompete["district_id"] = $req["district"];
                    array_push($arrayCustomerLocation, $newCompete);
                }
                if($req["county"] != "")
                {
                    $newCompete["county_id"] = $req["county"];
                    array_push($arrayCustomerLocation, $newCompete);
                }
            }

            CustomerLocations::where('customer_id',$customer->id)->where('main','1')->update(
                array_pop($arrayCustomerLocation)
            );
            return $customer;

        });

    }

    public function destroy(Customers $costumer): Customers
    {
        return DB::transaction(function () use ($costumer) {
            CustomerLocations::where('customer_id',$costumer->id)->delete();
            CustomerServices::where('customer_id',$costumer->id)->delete();
            $costumer->delete();
            return $costumer;
        });

    }


}

