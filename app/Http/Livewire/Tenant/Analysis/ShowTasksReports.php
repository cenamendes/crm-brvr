<?php

namespace App\Http\Livewire\Tenant\Analysis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant\TeamMember;
use Illuminate\Contracts\View\View;
use App\Interfaces\Tenant\Analysis\AnalysisInterface;

use App\Interfaces\Tenant\TeamMember\TeamMemberInterface;
use App\Interfaces\Tenant\CustomerLocation\CustomerLocationsInterface;
use App\Interfaces\Tenant\Customers\CustomersInterface;
use App\Interfaces\Tenant\Setup\Services\ServicesInterface;

class ShowTasksReports extends Component
{
    use WithPagination;

    public int $perPage;
    public string $searchString = '';

    protected object $analysisRepository;
    protected object $teamMembersRepository;
    protected object $customersRepository;
    protected object $serviceRepository;

    private ?object $analysis =  NULL;
    private ?object $members = NULL;
    private ?object $customers = NULL;
    private ?object $service = NULL;

    public int $technical = 0;
    public int $client = 0;
    public int $work = 0;

    public string $dateBegin = '';
    public string $dateEnd = '';


    public function boot(AnalysisInterface $interfaceAnalysis, TeamMemberInterface $interfaceTeamMember, CustomersInterface $interfaceCustomers, ServicesInterface $interfaceService)
    {
        $this->analysisRepository = $interfaceAnalysis;
        $this->teamMembersRepository = $interfaceTeamMember;
        $this->customersRepository = $interfaceCustomers;
        $this->serviceRepository = $interfaceService;
    }

    public function mount(): void
    {
        if (isset($this->perPage)) {
            session()->put('perPage', $this->perPage);
        } elseif (session('perPage')) {
            $this->perPage = session('perPage');
        } else {
            $this->perPage = 10;
        }

        $this->members = $this->teamMembersRepository->getTeamMembersAnalysis();
        $this->customers = $this->customersRepository->getCustomersAnalysis();
        $this->service = $this->serviceRepository->getServicesAnalysis();
        $this->analysis = $this->analysisRepository->getAllAnalysis($this->perPage);


    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
        session()->put('perPage', $this->perPage);
    }

    public function updatedSearchString(): void
    {
        $this->resetPage();
    }

    public function clearFilter(): void
    {
        $this->members = $this->teamMembersRepository->getTeamMembersAnalysis();
        $this->customers = $this->customersRepository->getCustomersAnalysis();
        $this->service = $this->serviceRepository->getServicesAnalysis();
        $this->analysis = $this->analysisRepository->getAllAnalysis($this->perPage);

        $this->technical = 0;
        $this->client = 0;
        $this->work = 0;
        $this->dateBegin = '';
        $this->dateEnd = '';

    }

    public function updatedTechnical(): void
    {
        $this->analysis = $this->analysisRepository->getAnalysisFilter($this->technical,$this->client,$this->work,$this->dateBegin,$this->dateEnd,$this->perPage);
    }

    public function updatedClient(): void
    {
        $this->analysis = $this->analysisRepository->getAnalysisFilter($this->technical,$this->client,$this->work,$this->dateBegin,$this->dateEnd,$this->perPage);
    }

    public function updatedWork(): void
    {
        $this->analysis = $this->analysisRepository->getAnalysisFilter($this->technical,$this->client,$this->work,$this->dateBegin,$this->dateEnd,$this->perPage);
    }

    public function updatedDateBegin(): void
    {
        $this->analysis = $this->analysisRepository->getAnalysisFilter($this->technical,$this->client,$this->work,$this->dateBegin,$this->dateEnd,$this->perPage);
        $this->dispatchBrowserEvent("contentChanged");

    }

    public function updatedDateEnd(): void
    {
        $this->analysis = $this->analysisRepository->getAnalysisFilter($this->technical,$this->client,$this->work,$this->dateBegin,$this->dateEnd,$this->perPage);
        $this->dispatchBrowserEvent("contentChanged");
    }


    public function paginationView()
    {
        return 'tenant.livewire.setup.pagination';
    }

    /**
     * List informations of customer location
     *
     * @return View
     */
    public function render(): View
    {
        $this->members = $this->teamMembersRepository->getTeamMembersAnalysis();
        $this->customers = $this->customersRepository->getCustomersAnalysis();
        $this->service = $this->serviceRepository->getServicesAnalysis();

        return view('tenant.livewire.analysis.show-tasks-reports', [
            'analysis' => $this->analysis,
            'members' => $this->members,
            'customers' => $this->customers,
            'services' => $this->service
        ]);
    }
}
