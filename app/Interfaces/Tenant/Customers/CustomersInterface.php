<?php

namespace App\Interfaces\Tenant\Customers;

use App\Models\Tenant\Customers;

use App\Models\Tenant\CustomerLocations;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\Tenant\Customers\CustomersFormRequest;
use App\Http\Requests\Tenant\CustomerContacts\CustomerContactsFormRequest;
use App\Http\Requests\Tenant\CustomerLocations\CustomerLocationsFormRequest;
use App\Models\Tenant\ContactsCustomers;
use Illuminate\Database\Eloquent\Collection;

interface CustomersInterface
{
    public function getAllCustomers($perPage): LengthAwarePaginator;

    public function getSearchedCustomer($searchString,$perPage): LengthAwarePaginator;

    public function getCustomersAnalysis(): Collection;

    public function getLocationsFromCustomer($customer_id,$searchString,$perPage): LengthAwarePaginator;

    public function add(CustomersFormRequest $request): Customers;

    public function update(Customers $customer,CustomersFormRequest $request): Customers;

    public function destroy(Customers $customer) : Customers;

}
