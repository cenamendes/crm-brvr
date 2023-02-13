<div>
    <div id="ajaxLoading" wire:loading.flex class="w-100 h-100 flex "
        style="background:rgba(255, 255, 255, 0.8);z-index:999;position:fixed;top:0;left:0;align-items: center;justify-content: center;">
        <div class="sk-three-bounce" style="background:none;">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <div class="card-header" wire:key="tenanttasksshow">
        <h4 class="card-title">{{ __('Tasks') }}</h4>
        <div class="col-3 text-right pr-0">
            <a href="{{ route('tenant.tasks.create') }}" class="btn btn-primary">{{ __('Add Task') }}</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div id="dataTables_wrapper" class="dataTables_wrapper">
                <div class="dataTables_length" id="dataTables_length">
                    <label>{{ __('Show') }}
                        <select name="perPage" wire:model="perPage">
                            <option value="10"
                                @if ($perPage == 10) selected @endif>10</option>
                            <option value="25"
                                @if ($perPage == 25) selected @endif>25</option>
                            <option value="50"
                                @if ($perPage == 50) selected @endif>50</option>
                            <option value="100"
                                @if ($perPage == 100) selected @endif>100</option>
                        </select>
                        {{ __('entries') }}</label>
                </div>
                <div id="dataTables_search_filter" class="dataTables_filter">
                    <label>{{ __('Search') }}:
                        <input type="search" name="searchString" wire:model="searchString"></label>
                </div>
            </div>
            <!-- display dataTable no-footer -->
            <table id="dataTables-data" class="table table-responsive-lg mb-0 table-striped">
                <thead>
                    <tr>
                        <th>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkAll" required="">
                                <label class="custom-control-label" for="checkAll"></label>
                            </div>
                        </th>
                        <th>{{ __('Reference') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Service') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('County') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasksList as $item)
                        <tr>
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="customCheckBox{{ $item->id }}"
                                        required="">
                                    <label class="custom-control-label" for="customCheckBox{{ $item->id }}"></label>
                                </div>
                            </td>
                            <td>{{ $item->reference }}</td>
                            <td>{{ $item->taskCustomer->short_name }}</td>
                            <td>
                                @forelse ($item->servicesToDo as $itemi)
                                    <div>{{ $itemi->service->name }}</div>
                                @empty
                                @endforelse
                            </td>
                            <td>
                                @isset($item->scheduled_date)
                                <i class="fa fa-calendar" aria-hidden="true"></i> {{ $item->scheduled_date }}<br>
                                <i class="fa fa-clock-o" aria-hidden="true"></i> {{ $item->scheduled_hour }}
                                @else
                                    <span title="{{ __('Not scheduled') }}"><i class="fa fa-calendar" aria-hidden="true" style="color:orange"></i> {{ $item->preview_date }}</span><br>
                                    <span title="{{ __('Not scheduled') }}"><i class="fa fa-clock-o" aria-hidden="true" style="color:orange"></i> {{ $item->preview_hour }}</span>
                                @endisset
                            </td>
                            <td>{{ $item->taskLocation->locationCounty->name }}
                            </td>
                            <td>
                                {{-- <div class="dropdown ml-auto text-right"> --}}
                                    {{-- <div class="btn-link" data-toggle="dropdown">
                                        <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <circle fill="#000000" cx="5" cy="12" r="2"></circle>
                                                <circle fill="#000000" cx="12" cy="12" r="2"></circle>
                                                <circle fill="#000000" cx="19" cy="12" r="2"></circle>
                                            </g>
                                        </svg>
                                    </div> --}}
                                    <!-- dropdown-menu dropdown-menu-right -->
                                    <div class="dropdown">
                                        <button class="btn btn-primary tp-btn-light sharp" type="button" data-toggle="dropdown">
                                            <span class="fs--1">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                        <circle fill="#000000" cx="5" cy="12" r="2"></circle>
                                                        <circle fill="#000000" cx="12" cy="12" r="2"></circle>
                                                        <circle fill="#000000" cx="19" cy="12" r="2"></circle>
                                                    </g>
                                                </svg>
                                            </span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if($item->status == 0 || $item->status == 1)
                                              @if (!isset($item->taskReports->reportStatus))
                                                <a class="dropdown-item" href="{{ route('tenant.tasks.edit', $item->id) }}">{{ __('Update Task') }}</a>
                                                <a class="dropdown-item" wire:click="askToSchedule({{ $item->id }})">{{ __('Schedule task') }}</a>
                                              @elseif($item->taskReports->reportStatus == 0 )
                                                <a class="dropdown-item" href="{{ route('tenant.tasks.edit', $item->id) }}">{{ __('Update Task') }}</a>
                                              @endif
                                            @endif
                                            <button class="dropdown-item btn-sweet-alert" data-type="form"
                                                data-route="{{ route('tenant.tasks.destroy', $item->id) }}"
                                                data-style="warning" data-csrf="csrf"
                                                data-text="{{ __('Do you want to delete this task?') }}"
                                                data-title="{{ __('Are you sure?') }}"
                                                data-btn-cancel="{{ __('No, do not delete!!') }}"
                                                data-btn-ok="{{ __('Yes, delete task!!') }}" data-method="DELETE">
                                                {{ __('Delete task') }}
                                            </button>
                                        </div>
                                    </div>
                                {{-- </div> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $tasksList->links() }}
        </div>
    </div>
</div>
@push('custom-scripts')
<script>
    // document.addEventListener('livewire:load', function () {
    //     restartObjects();
    //     jQuery('#selectedCustomer').select2();
    //     jQuery("#selectedCustomer").on("select2:select", function (e) { @this.selectedCustomer = jQuery('#selectedCustomer').find(':selected').val(); });

    //     jQuery('#selectedService').select2();
    //     jQuery("#selectedService").on("select2:select", function (e) { @this.selectedService = jQuery('#selectedService').find(':selected').val(); });
    // });

    window.addEventListener('contentChanged', event => {
        restartObjects();
    });

    // window.addEventListener('swalModalQuestion',function(e){
    //     if(e.detail.confirm) {
    //         swal.fire({
    //             title: e.detail.title,
    //             html: e.detail.message,
    //             type: e.detail.status,
    //             showCancelButton: true,
    //             confirmButtonColor: '#d33',
    //             confirmButtonText: e.detail.confirmButtonText,
    //             cancelButtonText: e.detail.cancellButtonText})
    //         .then((result) => {
    //             if(result.value) {
    //                 Livewire.emit('dispatchTask');
    //             }
    //         });
    //     } else {
    //         swal(e.detail.title, e.detail.message, e.detail.status);
    //     }
    // });

    window.addEventListener('swalModalQuestion',function(e){
            if(e.detail.confirm) {
                swal.fire({
                    title: e.detail.title,
                    html: e.detail.message,
                    type: e.detail.status,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: e.detail.confirmButtonText,
                    cancelButtonText: e.detail.cancellButtonText})
                .then((result) => {
                    if(result.value) {
                        Livewire.emit(e.detail.function);
                    }
                });
            } else {
                swal(e.detail.title, e.detail.message, e.detail.status);
            }
        });

    function restartObjects()
    {
        jQuery('#selectedService').select2();
        jQuery('#selectedLocation').select2();
        jQuery("#selectedLocation").on("select2:select", function (e) {
            @this.selectedLocation = jQuery('#selectedLocation').find(':selected').val();
        });
        jQuery('#selectedTechnician').select2();
        jQuery("#selectedTechnician").on("select2:select", function (e) {
            @this.set('selectedTechnician', jQuery('#selectedTechnician').find(':selected').val(), true)
        });

    }

    function formatDate(unixDate)
    {
        var date = new Date(unixDate);
        var year = date.getFullYear();
        var month = "0" + (date.getMonth()+1);
        var day = "0" + date.getDate();
        var formattedTime = year + '/' + month.substr(-2) + '/' + day.substr(-2);
        return formattedTime;
    }

</script>
</div>
@endpush
