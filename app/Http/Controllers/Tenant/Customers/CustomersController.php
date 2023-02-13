<?php

namespace App\Http\Controllers\Tenant\Customers;

use App\Models\Counties;
use App\Models\Districts;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Tenant\Customers;
use App\Models\Tenant\TeamMember;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CustomerContacts\CustomerContactsFormRequest;
use App\Interfaces\Tenant\Customers\CustomersInterface;
use App\Http\Requests\Tenant\Customers\CustomersFormRequest;
use App\Models\Tenant\ContactsCustomers;
use App\Models\Tenant\CustomerLocations;
use Illuminate\Http\RedirectResponse;

class CustomersController extends Controller
{
    private CustomersInterface $customersRepository;

    public function __construct(CustomersInterface $customersRepository)
    {
        $this->customersRepository = $customersRepository;
    }

    /**
     * Display the customers list.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('tenant.customers.index', [
            'themeAction' => 'table_datatable_basic',
            'status' => session('status'),
            'message' => session('message'),
        ]);
    }

    /**
     * Create new customer form
     *
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        
        $districts = Districts::all();
       
        $counties = Counties::all();
        $district = '';
        $county = '';
        $themeAction = 'form_element';
        $allAccountManagers = TeamMember::all();

        return view('tenant.customers.create', compact('themeAction', 'districts', 'counties', 'district', 'county', 'allAccountManagers'));
    }

    /**
     * Edit customer form
     *
     * @param Customers $customer
     * @return void
     */
    public function edit(Customers $customer) : View
    {
        $customer = Customers::where('id',$customer->id)->with('customerCounty')->first();
   
        $districts = Districts::all();
        $counties = Counties::all();
        $account_manager = $customer->account_manager;
           
        $themeAction = 'form_element_data_table';
       
        return view('tenant.customers.edit', compact('customer', 'themeAction', 'districts', 'counties', 'account_manager'));
    }

    /**
     * Insert a costumer
     *
     * @param CustomersFormRequest $request
     * @return RedirectResponse
     */
    public function store(CustomersFormRequest $request) : RedirectResponse
    {
        $this->customersRepository->add($request);
        return to_route('tenant.customers.index')
            ->with('message', __('Customer created with success!'))
            ->with('status', 'sucess');
    }

    /**
     * Update a costumer
     *
     * @param Customers $customer
     * @param CustomersFormRequest $request
     * @return RedirectResponse
     */
    public function update(Customers $customer, CustomersFormRequest $request) : RedirectResponse
    {
        $this->customersRepository->update($customer,$request);
        
        return to_route('tenant.customers.index')
            ->with('message', __('Customer updated with success!'))
            ->with('status', 'sucess');
    }

    /**
     * Delete a customer
     *
     * @param Customers $customer
     * @return RedirectResponse
     */
    public function destroy(Customers $customer) : RedirectResponse
    {
        $this->customersRepository->destroy($customer);

        return to_route('tenant.customers.index')
            ->with('message', __('Customer deleted with success!'))
            ->with('status', 'sucess');
    }

}
