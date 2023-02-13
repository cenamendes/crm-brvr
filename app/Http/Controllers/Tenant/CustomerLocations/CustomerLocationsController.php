<?php

namespace App\Http\Controllers\Tenant\CustomerLocations;

use Illuminate\View\View;
use App\Models\Tenant\Customers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\Tenant\CustomerLocations;
use App\Interfaces\Tenant\CustomerLocation\CustomerLocationsInterface;
use App\Http\Requests\Tenant\CustomerLocations\CustomerLocationsFormRequest;

class CustomerLocationsController extends Controller
{

    private CustomerLocationsInterface $customersLocationRepository;

    public function __construct(CustomerLocationsInterface $customersLocationRepository)
    {
        $this->customersLocationRepository = $customersLocationRepository;
    }

    /**
     * Return list of customer locations
     *
     * @return View
     */
    public function index(): View
    {
        return view('tenant.customerlocations.index', [
            'themeAction' => 'table_datatable_basic',
            'status' => session('status'),
            'message' => session('message'),
        ]);
    }

    /**
     * Create new customer location form
     *
     * @return View
     */
    public function create(): View
    {
        return view('tenant.customerlocations.create', [
            'themeAction' => 'form_element',
            'customerList' => Customers::all(),
        ]);
    }

    /**
     * Edit customer location form
     *
     * @param CustomerLocations $customerLocation
     * @return View
     */
    public function edit(CustomerLocations $customerLocation): View
    {
        return view('tenant.customerlocations.edit', [
            'customerLocation' => $customerLocation,
            'themeAction' => 'form_element',
            'customerList' => Customers::all(),
        ]);
    }

    /**
     * Insert new customer location
     *
     * @param CustomerLocationsFormRequest $request
     * @return RedirectResponse
     */
    public function store(CustomerLocationsFormRequest $request): RedirectResponse
    {
        $this->customersLocationRepository->add($request);

        return to_route('tenant.customer-locations.index')
            ->with('message', __('Customer location created with success!'))
            ->with('status', 'sucess');
    }

    /**
     * Update customer location
     *
     * @param CustomerLocations $customerLocation
     * @param CustomerLocationsFormRequest $request
     * @return RedirectResponse
     */
    public function update(CustomerLocations $customerLocation, CustomerLocationsFormRequest $request): RedirectResponse
    {
        $this->customersLocationRepository->update($customerLocation,$request);

        return to_route('tenant.customer-locations.index')
            ->with('message', __('Customer location updated with success!'))
            ->with('status', 'sucess');
    }

    /**
     * Delete customer location
     *
     * @param CustomerLocations $customerLocation
     * @return RedirectResponse
     */
    public function destroy(CustomerLocations $customerLocation): RedirectResponse
    {
        $this->customersLocationRepository->destroy($customerLocation);
        
        return to_route('tenant.customer-locations.index')
            ->with('message', __('Customer location deleted with success!'))
            ->with('status', 'sucess');
    }

}
