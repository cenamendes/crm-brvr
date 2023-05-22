<div>
    <div id="ajaxLoading" wire:loading.flex class="w-100 h-100 flex "
        style="background:rgba(255, 255, 255, 0.8);z-index:999;position:fixed;top:0;left:0;align-items: center;justify-content: center;">
        <div class="sk-three-bounce" style="background:none;">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $homePanel }}" data-toggle="tab" href="#homePanel"><i class="la la-home mr-2"></i> {{
                __('Customer') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $servicesPanel }}" data-toggle="tab" href="#servicesPanel"><i
                    class="flaticon-381-notepad mr-2"></i> {{ __('Service') }}</a>
        </li>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $techPanel }}" data-toggle="tab" href="#techPanel"><i
                    class="flaticon-381-calendar mr-2"></i> {{ __('Schedule') }}</a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link {{ $profile }}" data-toggle="tab" href="#contact"><i class="la la-phone mr-2"></i>
                Contacts</a>
        </li> --}}
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade {{ $homePanel }}" id="homePanel" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-0" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col">
                                        <div class="row form-group">
                                            <section class="col" wire:ignore>
                                                <label>{{ __('Customer Name') }}</label>
                                                <select name="selectedCustomer" id="selectedCustomer">
                                                    <option value="">{{ __('Select customer') }}</option>
                                                    @forelse ($customerList as $item)
                                                        <option value="{{ $item->id }}">{{ $item->vat }} | {{ $item->name }}</option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                            </section>
                                        </div>
                                        @if(isset($selectedCustomer) && $selectedCustomer <> '')
                                        <div class="row form-group">
                                            <section class="col-xl-3 col-xs-12">
                                                <label>{{ __('VAT') }}</label>
                                                <input type="text" name="vat" id="vat" class="form-control"
                                                    value="{{ $customer->vat }}" readonly>
                                            </section>
                                            <section class="col-xl-3 col-xs-12">
                                                <label>{{ __('Phone number') }}</label>
                                                <input type="text" name="phone" id="phone" class="form-control"
                                                    value="{{ $customer->contact }}" readonly>
                                            </section>
                                            <section class="col-xl-6 col-xs-12">
                                                <label>{{ __('Primary e-mail address') }}</label>
                                                <input type="text" name="email" id="email" class="form-control"
                                                    value="{{ $customer->email }}" readonly>
                                            </section>
                                        </div>
                                        <div class="row form-group">
                                            <section class="col-12">
                                                <label>{{ __('Customer Address') }}</label>
                                                <input type="text" name="address" id="address" class="form-control"
                                                    value="{{ $customer->address }}" readonly>
                                            </section>
                                        </div>
                                        <div class="row form-group">
                                            <section class="col-xl-2 col-xs-12">
                                                <label>{{ __('Zip Code') }}</label>
                                                <input type="text" name="zipcode" id="zipcode" class="form-control"
                                                    value="{{ $customer->zipcode }}" readonly>
                                            </section>
                                            <section class="col-xl-5 col-xs-12">
                                                <label>{{ __('District') }}</label>
                                                <input type="text" name="district" id="district" class="form-control"
                                                    value="{{ $customer->customerDistrict->name }}"
                                                    readonly>
                                            </section>
                                            <section class="col-xl-5 col-xs-12">
                                                <label>{{ __('County') }}</label>

                                                <input type="text" name="zipcode" id="zipcode" class="form-control" 
                                                    value="{{ $customer->customerCounty->name }}"
                                                    readonly>
                                            </section>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade {{ $servicesPanel }}" id="servicesPanel">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-0" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">
                        <div class="card-body">
                            <div class="basic-form">
                                @if(isset($selectedCustomer) && $selectedCustomer <> '' && isset($customerLocations))
                                    <div class="row form-group" wire:ignore>
                                        <div class="col">
                                            <label>{{ __('Location') }}</label>
                                            <select name="selectedLocation" id="selectedLocation">
                                                <option value="">{{ __('Please select location') }}</option>
                                                @forelse ($customerLocations as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->description }} | {{ $item->locationCounty->name }}
                                                    </option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($selectedCustomer) && $selectedLocation <> '' && isset($customerServicesList))
                                    <div class="row">
                                        <div class="col">
                                            <hr>
                                            <button class="btn btn-light" wire:click="addServiceForm">
                                                {{ __('Add Service') }}
                                                <span class="btn-icon-right"><i class="las la-plus-circle"></i></span>
                                            </button>
                                            <hr>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($numberOfSelectedServices) && $numberOfSelectedServices > 0)
                                    @for($i=0; $i < $numberOfSelectedServices; $i++)
                                        <div class="row form-group">
                                            <div class="col-9">
                                                <label>{{ __('Service Name') }}</label>
                                                <select name="selectedService_{{ $i }}" id="selectedService_{{ $i }}"
                                                    class="selectedService" data-rel="{{ $i }}">
                                                    <option value="">{{ __('Select Service') }}</option>
                                                    @forelse ($customerServicesList as $item)
                                                    <option value="{{ $item->service->id }}"
                                                        @if(isset($selectedServiceId[$i]) && $selectedServiceId[$i] == $item->service->id) selected
                                                        @endif>{{ $item->service->name }}</option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                            </div>
                                            <div class="col-3 text-right">
                                                <button class="btn btn-light" wire:click="removeServiceForm({{ $i }})">
                                                    {{ __('Remove Service') }}
                                                    <span class="btn-icon-right"><i class="las la-minus-circle"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col">
                                                <label>{{ __('Service description or notes ') }}</label>
                                                <textarea name="serviceDescription_{{ $i }}"
                                                    class="form-control serviceDesription" id="serviceDescription_{{ $i }}"
                                                    wire:model.defer="serviceDescription.{{ $i }}" data-rel="{{ $i }}"
                                                    rows="4">
                                                @if(isset($serviceDescription[$i]) && $serviceDescription[$i] != '') {{ $serviceDescription[$i] }} @endif
                                                </textarea>
                                            </div>
                                        </div>
                                    @endfor
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(isset($numberOfSelectedServices) && $numberOfSelectedServices > 0)

    <div class="tab-pane fade {{ $techPanel }}" id="techPanel">
        <div class="row">
            <div class="col-12">
                <div class="card mb-0" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">
                    <div class="card-body">
                        <div class="basic-form">
                            <div class="row">
                                <div class="col">
                                    @if(isset($numberOfSelectedServices) && $numberOfSelectedServices > 0)
                                    <div class="form-group row">
                                        <section class="col">
                                            <label>{{ __('Service Technician') }}</label>{{ $selectedTechnician }}
                                            <select name="selectedTechnician" id="selectedTechnician">
                                                <option value="">{{ __('Select Technician') }}</option>
                                                @if(isset($teamMembers))
                                                @forelse ($teamMembers as $item)
                                                <option value="{{ $item->id }}"
                                                    @if(isset($customer->account_manager) && !isset($selectedTechnician))
                                                        selected
                                                    @elseif(isset($selectedTechnician) && $item->id == $selectedTechnician) selected @endif>
                                                        {{ $item->name }}
                                                </option>
                                                @empty
                                                @endforelse
                                                @endif
                                            </select>
                                        </section>
                                    </div>
                                    <div class="form-group row" wire:ignore>
                                        <section class="col-3">
                                            <label>{{ __('Preview date') }}</label>
                                            <div class="input-group">
                                                <input type="text" name="preview_date" id="preview_date" class="form-control" class="datepicker-default" value="{{ $previewDate }}" wire:model.defer="previewDate">
                                                <span class="input-group-append"><span class="input-group-text">
                                                    <i class="fa fa-calendar-o"></i>
                                                </span></span>
                                            </div>
                                        </section>
                                        <section class="col-3">
                                            <label>{{ __('Preview hour') }}</label>
                                            <div class="input-group preview_hour">
                                                <input type="text" class="form-control" value="{{ $previewHour }}" wire:model.defer="previewHour">
                                                <span class="input-group-append"><span class="input-group-text"><i
                                                            class="fa fa-clock-o"></i></span></span>
                                            </div>
                                        </section>
                                        <section class="col-3">
                                            <label>{{ __('Scheduled date') }}</label>
                                            <div class="input-group">
                                                <input type="text" name="scheduled_date" id="scheduled_date"
                                                    class="form-control" value="{{ $scheduledDate }}" wire:model.defer="scheduledDate">
                                                <span class="input-group-append"><span class="input-group-text"><i
                                                            class="fa fa-calendar-o"></i></span></span>
                                            </div>
                                        </section>
                                        <section class="col-3">
                                            <label>{{ __('Scheduled hour') }}</label>
                                            <div class="input-group scheduled_hour">
                                                <input type="text" class="form-control" value="{{ $scheduledHour }}" wire:model.defer="scheduledHour">
                                                <span class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-clock-o"></i></span>
                                                </span>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="form-group row">
                                        <section class="col">
                                            <label>{{ __('Adicional notes') }}</label>
                                            <textarea name="taskAdditionalDescription" class="form-control"
                                                id="taskAdditionalDescription" wire:model.defer="taskAdditionalDescription"
                                                rows="4">
@if($additional_description != '') {{ $additional_description }} @endif
</textarea>
                                        </section>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
    @endif
    <div class="card">
        <div class="card-footer justify-content-between">
            <div class="row">
                <div class="col text-right">
                    <a wire:click="cancel" class="btn btn-secondary mr-2">
                        {!! $cancelButton !!}
                    </a>
                    <a wire:click="saveTask" class="btn btn-primary">
                        <i class="las la-check mr-2"></i>{{ $actionButton }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('custom-scripts')
    <script>
        var services = [];
        document.addEventListener('livewire:load', function () {
            restartObjects();
        });

        window.addEventListener('swal',function(e){
            swal.fire({
                title: e.detail.title,
                html: e.detail.message,
                showCancelButton: true,
                cancelButtonText: "Cancelar",
                type: "error",

            }).then((result) => {  
                if(result.value){

                    window.location.replace("http://"+window.location.hostname+"/services/create");

                    restartObjects();
                    if(e.detail.function == 'client')
                    {
                        location.reload();
                    }
                }                
            })
           
        });

        function restartObjects()
        {

            /* valido */
            jQuery('#selectedCustomer').select2();
            jQuery("#selectedCustomer").on("select2:select", function (e) {
                @this.set('selectedCustomer', jQuery('#selectedCustomer').find(':selected').val());
            });

            /* valido */
            jQuery('#selectedLocation').select2();
            jQuery("#selectedLocation").on("select2:select", function (e) {
                @this.set('selectedLocation', jQuery('#selectedLocation').find(':selected').val());
            });

      
            jQuery('#selectedTechnician').select2();
            jQuery("#selectedTechnician").on("select2:select", function (e) {
                @this.set('selectedTechnician', jQuery('#selectedTechnician').find(':selected').val(), true)
            });
            jQuery('.selectedService').each(function() {
                var selectId = '#' + jQuery(this).attr('id');
                jQuery(selectId).off('select2:select');
                jQuery(selectId).select2();
                jQuery(selectId).on('select2:select', function (e) {
                    @this.set('selectedServiceId.' + jQuery(selectId).attr('data-rel'), jQuery(selectId).find(':selected').val());
                });
            });
            jQuery('#preview_date').pickadate({
                monthsFull:["{!! __('January') !!}","{!! __('February') !!}","{!! __('March') !!}","{!! __('April') !!}","{!! __('May') !!}","{!! __('June') !!}","{!! __('July') !!}","{!! __('August') !!}","{!! __('September') !!}","{!! __('October') !!}","{!! __('November') !!}","{!! __('December') !!}"],
                weekdaysShort: ["{!! __('Sun') !!}","{!! __('Mon') !!}","{!! __('Tue') !!}","{!! __('Wed') !!}","{!! __('Thu') !!}","{!! __('Fri') !!}","{!! __('Sat') !!}"],
                today: "{!! __('today') !!}",
                clear: "{!! __('clear') !!}",
                close: "{!! __('close') !!}",
                onSet: function(thingSet) {
                    @this.set('previewDate', formatDate(thingSet.select), true);
                    jQuery('#preview_date').val(formatDate(thingSet.select));
                }
            });
            jQuery('#scheduled_date').pickadate({
                monthsFull:["{!! __('January') !!}","{!! __('February') !!}","{!! __('March') !!}","{!! __('April') !!}","{!! __('May') !!}","{!! __('June') !!}","{!! __('July') !!}","{!! __('August') !!}","{!! __('September') !!}","{!! __('October') !!}","{!! __('November') !!}","{!! __('December') !!}"],
                weekdaysShort: ["{!! __('Sun') !!}","{!! __('Mon') !!}","{!! __('Tue') !!}","{!! __('Wed') !!}","{!! __('Thu') !!}","{!! __('Fri') !!}","{!! __('Sat') !!}"],
                today: "{!! __('today') !!}",
                clear: "{!! __('clear') !!}",
                close: "{!! __('close') !!}",
                onSet: function(thingSet) {
                    @this.set('scheduledDate', formatDate(thingSet.select), true);
                    jQuery('#scheduled_date').val(formatDate(thingSet.select));
               }
            });
            $('.preview_hour').clockpicker({
                donetext: '<i class="fa fa-check" aria-hidden="true"></i>',
            }).find('input').change(function () {
                @this.set('previewHour', this.value, true);
            });
            $('.scheduled_hour').clockpicker({
                donetext: '<i class="fa fa-check" aria-hidden="true"></i>',
            }).find('input').change(function () {
                @this.set('scheduledHour', this.value, true);
            });
        }

        window.addEventListener('loading', function(e) {
            @this.loading();
        })

        window.addEventListener('newService', function(e) {
            jQuery('.selectedService').each(function() {
                var selectId = '#' + jQuery(this).attr('id');
                jQuery(selectId).off('select2:select');
                jQuery(selectId).select2();
                jQuery(selectId).on('select2:select', function (e) {
                    @this.set('selectedServiceId.' + jQuery(selectId).attr('data-rel'), jQuery(selectId).find(':selected').val());
                });
            });
        });

        window.addEventListener('contentChanged', function(e) {
            restartObjects();
        })

        window.addEventListener('refreshPage', function(e) {
            window.location.reload();
        })
        
  

              
       
        function formatDate(unixDate)
        {
            var date = new Date(unixDate);
            var year = date.getFullYear();
            var month = "0" + (date.getMonth()+1);
            var day = "0" + date.getDate();
            var formattedTime = year + '-' + month.substr(-2) + '-' + day.substr(-2);
            return formattedTime;
        }
    </script>
    @endpush
</div>
{{--

<x-tenant.tasks.form :action="route('tenant.tasks.store')"
    :update="false"
    :customerList="$customerList"
    :homePanel="$homePanel"
    :servicesPanel="$servicesPanel"
    :techPanel="$techPanel"
    :selectedCustomer="$selectedCustomer"
    :selectedService="$selectedService"
    :customer="$customer"
    :customerLocations="$customerLocations"
    :selectedLocation="$selectedLocation"
    :serviceList="$serviceList"

    {{-- :serviceList="$serviceList"
    :selectedCustomer="$selectedCustomer"
    :selectedService="$selectedService"
    :customer="$customer"
    :service="$service"

    :profile="$profile"
    :start_date="$start_date"
    :end_date="$end_date"
    :type="$type"
    :locations="$locations"
    buttonAction="{{ __('Create') }}"
    formTitle="{{ __('Create Task') }}" />
 --}}
