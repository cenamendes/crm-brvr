<?php

namespace App\Http\Controllers\Tenant\CustomerContacts;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CustomerContacts;
use App\Models\Tenant\CustomerLocations;
use App\Models\Tenant\Customers;
use App\Http\Requests\Tenant\CustomerContacts\CustomerContactsFormRequest;
use App\Interfaces\Tenant\CustomerContacts\CustomerContactsInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerContactsController extends Controller
{
    private CustomerContactsInterface $customersContactsRepository;

    /**
     * Prepare the customers contacts repository
     *
     * @param CustomerContactsInterface $customersContactsRepository
     */
    public function __construct(CustomerContactsInterface $customersContactsRepository)
    {
        $this->customersContactsRepository = $customersContactsRepository;
    }

    /**
     * Open page for creating contacts
     *
     * @param integer $customer
     * @return View
     */
    public function show(int $customer): View
    {
       
        $themeAction = 'form_element';
        $customer = Customers::where('id', $customer)->first();
        $customerLocation = CustomerLocations::where('customer_id', $customer->id)->get();
        
        return view('tenant.customers.createContact', compact('themeAction', 'customer', 'customerLocation'));
    }

    /**
     * Open page for editing a contact
     *
     * @param CustomerContacts $customerContact
     * @return View
     */
    public function edit(CustomerContacts $customerContact): View
    {
        $themeAction = 'form_element';
        $customerId = $customerContact->id;
        $customer = Customers::where('id', $customerContact->id)->first();
        $customerLocation = CustomerLocations::where('customer_id', $customerContact->customer_id)->get();
        return view('tenant.customers.editContact', compact('themeAction', 'customerId', 'customer', 'customerLocation', 'customerContact'));
    }

    /**
     * Store a contact from a costumer
     *
     * @param CustomerContactsFormRequest $request
     * @return RedirectResponse
     */
    public function store(CustomerContactsFormRequest $request): RedirectResponse
    {
        $this->customersContactsRepository->addCustomerContact($request);
        return to_route('tenant.customers.edit', $request->customer_id)
            ->with('message', __('Customer Contact created with success!'))
            ->with('status', 'sucess');
    }

    /**
     * Update a contact from a costumer
     *
     * @param integer $contact
     * @param CustomerContactsFormRequest $request
     * @return RedirectResponse
     */
    public function update(int $contact, CustomerContactsFormRequest $request): RedirectResponse
    {
        $this->customersContactsRepository->updateCustomerContact($contact, $request);

        return to_route('tenant.customers.edit', $request->customer_id)
            ->with('message', __('Customer Contact updated with success!'))
            ->with('status', 'sucess');
    }

    /**
     * Delete a contact from a costumer
     *
     * @param int $customerId
     * @return RedirectResponse
     */
    public function destroy(int $contactCustomer): RedirectResponse
    {
        $this->customersContactsRepository->deleteCustomerContact($contactCustomer);

        return to_route('tenant.customers.index')
            ->with('message', __('Customer Contact deleted with success!'))
            ->with('status', 'sucess');
    }
}
