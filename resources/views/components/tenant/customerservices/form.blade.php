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
            <a class="nav-link active" data-toggle="tab" href="#homePanel"><i class="la la-home mr-2"></i> {{ __('Customer') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#servicesPanel"><i class="flaticon-381-notepad mr-2"></i> {{ __('Service') }}</a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link {{ $profile }}" data-toggle="tab" href="#contact"><i class="la la-phone mr-2"></i> Contacts</a>
        </li> --}}
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="homePanel" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">
                        <div class="card-body">
                            <div class="basic-form">
                                <form action="{{ $action }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @if ($update)
                                        @method('PUT')
                                    @endif
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group row">
                                            <section class="col">
                                                @php
                                                if(!isset($service->customer_id)) {
                                                    echo '<label>' . __('Customer Name') . '</label>';
                                                    echo '<select name="selectedCustomer" id="selectedCustomer" class="form-control" wire:change="updateCustomer($event.target.value)" wire:model="selectedCustomer">';
                                                    echo '<option value="">' . __('Select Customer') . '</option>';
                                                    foreach ($customerList as $item) {
                                                        echo '<option value="' . $item->id . '">' . $item->name . '</option>';
                                                    }
                                                    echo '</select>';
                                                } else {
                                                    echo '<input type="hidden" name="selectedCustomer" id="selectedCustomer" value="' . $service->customer_id . '" class="selectedCustomerHidden" wire:model="selectedCustomer">';
                                                }
                                                @endphp
                                            </section>
                                        </div>
                                        @if(isset($customer) && $customer != '')
                                        <div class="form-group row">
                                            <section class="col-xl-9 col-xs-12">
                                                <label>{{ __('Customer Name') }}</label>
                                                <input type="text" name="name" id="name" class="form-control"
                                                    value="{{ $customer->name }}" readonly>
                                            </section>
                                            <section class="col-xl-3 col-xs-12">
                                                <label>{{ __('VAT') }}</label>
                                                <input type="text" name="vat" id="vat" class="form-control"
                                                    value="{{ $customer->vat }}" readonly>
                                            </section>
                                        </div>
                                        <div class="form-group row">
                                            <section class="col-xl-3 col-xs-12">
                                                <label>{{ __('Phone number') }}</label>
                                                <input type="text" name="phone" id="phone" class="form-control"
                                                    value="{{ $customer->contact }}" readonly>
                                            </section>
                                            <section class="col-xl-9 col-xs-12">
                                                <label>{{ __('Primary e-mail address') }}</label>
                                                <input type="text" name="email" id="email" class="form-control"
                                                    value="{{ $customer->email }}" readonly>
                                            </section>
                                        </div>
                                        <div class="form-group row">
                                            <section class="col-12">
                                                <label>{{ __('Customer Address') }}</label>
                                                <input type="text" name="address" id="address" class="form-control"
                                                    value="{{ $customer->address }}" readonly>
                                            </section>
                                        </div>
                                        <div class="form-group row">
                                            <section class="col-xl-2 col-xs-12">
                                                <label>{{ __('Zip Code') }}</label>
                                                <input type="text" name="zipcode" id="zipcode" class="form-control"
                                                    value="{{ $customer->zipcode }}" readonly>
                                            </section>
                                            <section class="col-xl-5 col-xs-12">
                                                <label>{{ __('District') }}</label>
                                                <input type="text" name="district" id="district" class="form-control"
                                                    value="{{ $customer->customerDistrict->name }}" readonly>
                                            </section>
                                            <section class="col-xl-5 col-xs-12">
                                                <label>{{ __('County') }}</label>
                                                <input type="text" name="zipcode" id="zipcode" class="form-control"
                                                    value="{{ $customer->customerCounty->name }}" readonly>
                                            </section>
                                        </div>
                                        @endisset
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="servicesPanel">
            <div class="row">
                <div class="col-12">
                    <div class="card" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="form-group row" wire:ignore>
                                    <section class="col">
                                        <label>{{ __('Service Name') }}</label>
                                        <select name="selectedService" id="selectedService" class="form-control" wire:model.defer = "selectedService">
                                            <option value="">{{ __('Select Service') }}</option>

                                            @forelse ($serviceList as $item)
                                            <option value="{{ $item->id }}" @if($service != "") @if($item->id == $service->service_id) selected @endif @endif >{{ $item->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </section>
                                </div>
                                {{-- @if(isset($service) && $service != '') --}}
                                <div class="form-group row" wire:ignore>
                                    <section class="col-4">
                                        <label>{{ __('Start Date') }}</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"  class="datepicker-default"
                                        @if(isset($service->start_date) && $service->start_date != '1970-01-01') value="{{ $service->start_date }}"@endisset
                                        @if(null !== old('start_date'))value="{{ old('start_date') }}"@endisset placeholder="{{ __('Select Start Date') }}">

                                    </section>
                                    <section class="col-4">
                                        <label>{{ __('End Date') }}</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                        @if(isset($service->end_date) && $service->end_date != '1970-01-01') value="{{ $service->end_date }}"@endisset
                                        @if(null !== old('end_date'))value="{{ old('end_date') }}"@endisset  placeholder="{{ __('Select End Date') }}">

                                    </section>
                                    <section class="col-4">
                                        <label>{{ __('Contract type') }}</label>
                                        <input type="text" name="type" id="type" class="form-control"
                                        @isset($service->type) value="{{ $service->type }}"@endisset
                                        @if(null !== old('type'))value="{{ old('type') }}"@endisset  placeholder="{{ __('Select Contract Type') }}">
                                    </section>
                                </div>
                                {{-- @endisset --}}
                                <div class="form-group row">
                                    <section class="col">
                                        <label>{{ __('Customer Location') }}</label>
                                        <select name="selectedLocation" id="selectedLocation" class="form-control" wire:model.defer = "selectedLocation">
                                            <option value="" selected>{{ __('Select Customer Location') }}</option>
                                            @if(isset($customerLocations) && $customerLocations != '')
                                                @forelse ($customerLocations as $item)
                                                    <option value="{{ $item->id }}" @if($service != "") @if($item->id == $service->location_id) selected @endif @endif>
                                                        {{ $item->description }}
                                                    </option>
                                                @empty
                                                @endforelse
                                            @endif
                                        </select>
                                    </section>
                                </div>
                                <div class="form-group row">
                                    <section class="col">
                                        <label>{{ __('Alert time (in days)') }}</label>
                                        <input type="number" id="alert" name="alert" class="form-control"
                                        @isset($service->alert) value="{{ $service->alert }}" @endisset
                                        @if(null !== old('alert'))value="{{ old('alert') }}"@endisset placeholder="{{ __("Select Time to alert")}}">
                                    </section>
                                </div>
                                {{-- <section class="form-group row">
                                    <div class="col-3">
                                        <a href="{{ route('tenant.services.index') }}"
                                            class="btn btn-secondary"><i class="las la-angle-double-left mr-2"></i>{{
                                            __('Back') }}</a>
                                    </div>
                                    <div class="col-9 text-right">
                                        <a wire:click="cancel" class="btn btn-secondary mr-2">
                                            <i class="las la-times mr-2"></i>{{__("Cancel")}}
                                        </a>
                                        <button type="submit" class="btn btn-primary">{{ $buttonAction }}</button>
                                    </div>
                                </section>
                            </form> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-footer justify-content-between">
            <div class="row">
                <div class="col text-right">
                    <a href="{{ route('tenant.services.index') }}" class="btn btn-secondary mr-2">{{
                        __('Back') }}
                        <span class="btn-icon-right"><i class="las la-angle-double-left"></i></span>
                    </a>
                    <button type="submit" style="border:none;background:none;">
                        <a type="submit" class="btn btn-primary"  role="button">
                            {{ $buttonAction }}
                            <span class="btn-icon-right"><i class="las la-check mr-2"></i></span>
                        </a>
                    </button>
                </div>
            </div>
        </div>
    </div>
    </form>

    @push('custom-scripts')
    <script>
        // document.addEventListener('livewire:load', function () {
           // restartObjects();
            // if(jQuery('.selectedCustomerHidden').lenght == 0) {
            //     jQuery('#selectedCustomer').select2();
            //     jQuery("#selectedCustomer").on("select2:select", function (e) { @this.selectedCustomer = jQuery('#selectedCustomer').find(':selected').val(); });
            // }
            // jQuery('#selectedLocation').select2();
            // jQuery("#selectedLocation").on("select2:select", function (e) {
            //     @this.set('selectedLocation', jQuery('#selectedLocation').find(':selected').val(), true)
            // });
            // jQuery('#selectedService').select2();
            // jQuery("#selectedService").on("select2:select", function (e) {
            //     @this.set('selectedService', jQuery('#selectedService').find(':selected').val(), true)
            // });
        // });

        window.addEventListener('swal',function(e){
            if(e.detail.confirm) {
                var page = e.detail.page;
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
                        Livewire.emit('resetChanges');
                        if(page != "edit")
                        {
                            jQuery("#selectedCustomer").val("");
                            jQuery("#selectedLocation").val("");
                            jQuery("#selectedService").val("");
                            jQuery("#start_date").val("");
                            jQuery("#end_date").val("");
                            jQuery("#type").val("");
                        }
                    } else {
                        Livewire.emit('keepChanges');
                    }
                });
            } else {
                swal(e.detail.title, e.detail.message, e.detail.status);
            }
        });

        function restartObjects()
        {
            if(jQuery('.selectedCustomerHidden').lenght == 0) {
                jQuery('#selectedCustomer').select2();
                jQuery("#selectedCustomer").on("select2:select", function (e) { @this.selectedCustomer = jQuery('#selectedCustomer').find(':selected').val(); });
            }
            jQuery('#selectedLocation').select2();
            jQuery('#selectedService').select2();

            jQuery('#start_date').pickadate({
                monthsFull: ["{!! __('January') !!}","{!! __('February') !!}","{!! __('March') !!}","{!! __('April') !!}","{!! __('May') !!}","{!! __('June') !!}","{!! __('July') !!}","{!! __('August') !!}","{!! __('September') !!}","{!! __('October') !!}","{!! __('November') !!}","{!! __('December') !!}"],
                weekdaysShort: ["{!!__('Sun') !!}","{!!__('Mon') !!}","{!!__('Tue') !!}","{!!__('Wed') !!}","{!!__('Thu') !!}","{!!__('Fri') !!}","{!!__('Sat') !!}"],
                today: "{!! __('today') !!}",
                clear: "{!! __('clear') !!}",
                close: "{!! __('close') !!}",
                onSet: function(thingSet) {
                    @this.set('start_date', formatDate(thingSet.select), true);
                    jQuery('#start_date').val(formatDate(thingSet.select))
                },
            });

            jQuery('#end_date').pickadate({
                monthsFull:["{!! __('January') !!}","{!! __('February') !!}","{!! __('March') !!}","{!! __('April') !!}","{!! __('May') !!}","{!! __('June') !!}","{!! __('July') !!}","{!! __('August') !!}","{!! __('September') !!}","{!! __('October') !!}","{!! __('November') !!}","{!! __('December') !!}"],
                weekdaysShort: ["{!!__('Sun') !!}","{!!__('Mon') !!}","{!!__('Tue') !!}","{!!__('Wed') !!}","{!!__('Thu') !!}","{!!__('Fri') !!}","{!!__('Sat') !!}"],
                today: "{!! __('today') !!}",
                clear: "{!! __('clear') !!}",
                close: "{!! __('close') !!}",
                onSet: function(thingSet) {
                    @this.set('end_date', formatDate(thingSet.select), true)
                    jQuery('#end_date').val(formatDate(thingSet.select))
               }
            });
        }

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
</div>
@endpush
