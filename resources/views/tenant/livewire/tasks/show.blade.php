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
        @if(Auth::user()->type_user !="2")
            <div class="col-xl-3 col-xs-6 text-right pr-0">
                <a href="{{ route('tenant.tasks.create') }}" class="btn btn-primary">{{ __('Add Task') }}</a>
            </div>
        @endif
    </div>
    <div class="card-body">

       <!-- Inicio do Filtro  -->

       <div id="accordion-one" class="accordion accordion-primary" wire:ignore>
        <div class="accordion__item">
            <div class="accordion__header rounded-lg collapsed" data-toggle="collapse" data-target="#default_collapseOne" aria-expanded="false">
                <span class="accordion__header--text">{{__("Filters")}}</span>
                <span class="accordion__header--indicator"></span>
            </div>
            <div id="default_collapseOne" class="accordion__body collapse" data-parent="#accordion-one">
                <div class="accordion__body--text">
                    <div class="col-12" style="margin-bottom:25px;padding-left:0px;">
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label>{{__("Select Technical")}}</label>
                                    <select name="selectTechnical" id="selectTechnical" class="form-control" wire:model="technical">
                                        <option value="0">{{__("All")}}</option>
                                        @foreach ($members as $member)
                                        <option value={{$member->id}}>{{$member->name}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>{{__("Select Customer")}}</label>
                                    <select class="form-control" name="selectCustomer" id="selectCustomer" wire:model="client">
                                        <option value="0">{{__("All")}}</option>
                                            @foreach ($customers as $customer)
                                                <option value={{$customer->id}}>{{$customer->short_name}}</option> 
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>{{__("Select Type of Task")}}</label>
                                    <select class="form-control" name="selectType" id="selectType" wire:model="typeTask">
                                        <option value="4">{{__("All")}}</option>
                                        <option value="3">{{__("Not Initiated")}}</option>
                                        <option value="0">{{__("Agendadas")}}</option>
                                        <option value="1">{{__("Em Curso")}}</option>
                                        <option value="2">{{__("Finalizadas")}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>{{__("Select Ordenation")}}</label>
                                    <select class="form-control" name="ordenation" id="ordenation" wire:model="ordenation">
                                        <option value="desc">{{__("Newest to Oldest")}}</option>
                                        <option value="asc">{{__("Oldest to Newest")}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>{{__("Select Service")}}</label>
            
                                    <select class="form-control" name="workDescription" id="workDescription" wire:model="work">
                                        <option value="0">{{ __("All") }}</option>
                                        @foreach ($services as $service)
                                            <option value="{{$service->id}}">{{$service->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>{{__("Initial Date")}}</label>
                                    <div class="input-group" wire:ignore>
                                        <input id="dateBegin" class="form-control" type="text" wire:model="dateBegin" placeholder="{{ __("Date Begin") }}">
                                        <span class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar-o"></i></span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>{{__("Final Date")}}</label>
                                    <div class="input-group" wire:ignore>
                                        <input id="dateEnd" class="form-control picker__input" type="text" wire:model="dateEnd" placeholder="{{ __("Date End") }}">
                                        <span class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar-o"></i></span></span>
                                    </div>
                                </div>
                            </div>
                        </div> 
            
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="button" id="clearFilter" wire:click="clearFilter" class="btn-sm btn btn-primary">{{__("Clear Filter")}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Fim do Filtro -->  

      <div class="row">
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
                        <th>Resumo</th>
                        <th>{{ __('Technical') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('County') }}</th>
                        <th>{{ __('State of Task') }}</th>
                        @if(Auth::user()->type_user !="2")
                            <th>{{ __('Action') }}</th>
                        @endif
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
                            <td>{{ $item->resume }}</td>
                            <td>{{ $item->tech->name }}</td>
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
                                @if($item->taskReports != null)
                                  @if($item->taskReports->reportStatus == 0)
                                        {{ __("Agendada") }}
                                  @elseif($item->taskReports->reportStatus == 1)
                                    {{ __("Em Curso") }}
                                  @else
                                    {{ __("Finalizado") }}
                                  @endif
                                @else
                                    {{__("Não iniciada")}}
                                @endif

                            </td>

                           
                                <td>
                                        <!-- dropdown-menu dropdown-menu-right -->

                                    @if(Auth::user()->type_user != 2)

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

                                                @php
                                                    $user = \App\Models\Tenant\TeamMember::where('id',$item->tech_id)->first();
                                                @endphp
                                                
                                                @if($item->status == 0 || $item->status == 1)
                                                <a class="dropdown-item" href="{{ route('tenant.tasks.edit', $item->id) }}">{{ __('Update Task') }}</a>
                                                    @if (!isset($item->taskReports->reportStatus))
                                                            {{-- <a class="dropdown-item" href="{{ route('tenant.tasks.edit', $item->id) }}">{{ __('Update Task') }}</a> --}}
                                                            @if(Auth::user()->type_user == 0 || $user->user_id == Auth::user()->id )
                                                                <a class="dropdown-item" wire:click="askToSchedule({{ $item->id }})">{{ __('Schedule task') }}</a>
                                                            @endif
                                
                                                    @endif
                                                    {{-- <a class="dropdown-item" href="{{ route('tenant.tasks.edit', $item->id) }}">{{ __('Update Task') }}</a> --}}
                                                @endif

                                                @if(Auth::user()->type_user == 0 || $user->user_id == Auth::user()->id )
                                                    <button class="dropdown-item btn-sweet-alert" data-type="form"
                                                        data-route="{{ route('tenant.tasks.destroy', $item->id) }}"
                                                        data-style="warning" data-csrf="csrf"
                                                        data-text="{{ __('Do you want to delete this task?') }}"
                                                        data-title="{{ __('Are you sure?') }}"
                                                        data-btn-cancel="{{ __('No, do not delete!!') }}"
                                                        data-btn-ok="{{ __('Yes, delete task!!') }}" data-method="DELETE">
                                                        {{ __('Delete task') }}
                                                    </button>
                                                @endif

                                            </div>
                                        </div>
                                   
                                    @endif
                                 
                                </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $tasksList->links() }}
        </div>
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

    window.addEventListener('livewire:load', event => {
        restartObjects();
    });

    window.addEventListener('contentChanged', event => {
        restartObjects();
    });

   
    window.addEventListener('swalModalQuestion',function(e){
        console.log("DO EDIT");
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

        jQuery('.input-group #dateBegin').pickadate({
            monthsFull: ["{!!__('January') !!}", "{!!__('February') !!}","{!!__('March') !!}","{!!__('April') !!}","{!!__('May') !!}","{!!__('June') !!}","{!!__('July') !!}","{!!__('August') !!}","{!!__('September') !!}","{!!__('October') !!}","{!!__('November') !!}","{!!__('December') !!}"],
            weekdaysShort: ["{!!__('Sun') !!}","{!! __('Mon') !!}","{!! __('Tue') !!}", "{!! __('Wed') !!}","{!! __('Thu') !!}", "{!! __('Fri') !!}", "{!! __('Sat') !!}"],
            today: "{!! __('today') !!}",
            clear: "{!! __('clear') !!}",
            close: "{!! __('close') !!}",
            onSet: function(thingSet) {
                @this.set('dateBegin', formatDate(thingSet.select));
                //jQuery('#dateBegin').val(formatDate(thingSet.select));
                }
        });

        jQuery('.input-group #dateEnd').pickadate({
            monthsFull: ["{!! __('January') !!}","{!! __('February') !!}","{!! __('March') !!}","{!! __('April') !!}","{!! __('May') !!}","{!! __('June') !!}","{!! __('July') !!}","{!! __('August') !!}","{!! __('September') !!}","{!! __('October') !!}","{!! __('November') !!}","{!! __('December') !!}"],
            weekdaysShort: ["{!!__('Sun') !!}","{!!__('Mon') !!}","{!!__('Tue') !!}","{!!__('Wed') !!}","{!!__('Thu') !!}","{!!__('Fri') !!}","{!!__('Sat') !!}"],
            today: "{!! __('today') !!}",
            clear: "{!! __('clear') !!}",
            close: "{!! __('close') !!}",
            onSet: function(thingSet) {
                @this.set('dateEnd', formatDate(thingSet.select));
                //jQuery('#dateEnd').val(formatDate(thingSet.select));
                }
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
