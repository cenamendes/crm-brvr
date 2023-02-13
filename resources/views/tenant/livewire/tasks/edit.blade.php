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
                                                <select name="selectedCustomer" id="selectedCustomer" disabled>
                                                    <option value="{{ $taskToUpdate->taskCustomer->id }}" selected>{{
                                                        $taskToUpdate->taskCustomer->name }}</option>
                                                </select>
                                            </section>
                                        </div>
                                        <div class="row form-group">
                                            <section class="col-3">
                                                <label>{{ __('VAT') }}</label>
                                                <input type="text" name="vat" id="vat" class="form-control"
                                                    value="{{ $taskToUpdate->taskCustomer->vat }}" readonly>
                                            </section>
                                            <section class="col-3">
                                                <label>{{ __('Phone number') }}</label>
                                                <input type="text" name="phone" id="phone" class="form-control"
                                                    value="{{ $taskToUpdate->taskCustomer->contact }}" readonly>
                                            </section>
                                            <section class="col-6">
                                                <label>{{ __('Primary e-mail address') }}</label>
                                                <input type="text" name="email" id="email" class="form-control"
                                                    value="{{ $taskToUpdate->taskCustomer->email }}" readonly>
                                            </section>
                                        </div>
                                        <div class="row form-group">
                                            <section class="col-12">
                                                <label>{{ __('Customer Address') }}</label>
                                                <input type="text" name="address" id="address" class="form-control"
                                                    value="{{ $taskToUpdate->taskCustomer->address }}" readonly>
                                            </section>
                                        </div>
                                        <div class="row form-group">
                                            <section class="col-2">
                                                <label>{{ __('Zip Code') }}</label>
                                                <input type="text" name="zipcode" id="zipcode" class="form-control"
                                                    value="{{ $taskToUpdate->taskCustomer->zipcode }}" readonly>
                                            </section>
                                            <section class="col-5">
                                                <label>{{ __('District') }}</label>
                                                <input type="text" name="district" id="district" class="form-control"
                                                    value="{{ $taskToUpdate->taskCustomer->customerDistrict->name }}"
                                                    readonly>
                                            </section>
                                            <section class="col-5">
                                                <label>{{ __('County') }}</label>
                                                <input type="text" name="zipcode" id="zipcode" class="form-control"
                                                    value="{{ $taskToUpdate->taskCustomer->customerCounty->name }}"
                                                    readonly>
                                            </section>
                                        </div>
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
                                <div class="row form-group">
                                    <div class="col">
                                        <label>{{ __('Location') }}</label>
                                        <select name="selectedLocation" id="selectedLocation" disabled>
                                            <option value="{{ $taskToUpdate->location_id }}" selected>
                                                {{ $taskToUpdate->taskLocation->description }} | {{ $taskToUpdate->taskLocation->locationCounty->name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
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
                                            <label>{{ __('Service Technician') }}</label>
                                            <select name="selectedTechnician" id="selectedTechnician">
                                                <option value="">{{ __('Select Technician') }}</option>
                                                @if(isset($teamMembers))
                                                @forelse ($teamMembers as $item)
                                                <option value="{{ $item->id }}" @if(isset($selectedTechnician) &&
                                                    $item->id == $selectedTechnician) selected @endif>{{ $item->name }}
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
                                                <input type="text" name="preview_date" id="preview_date"
                                                    class="form-control" class="datepicker-default" @if($previewDate)
                                                    value="{{ $previewDate }}" @endif>
                                                <span class="input-group-append"><span class="input-group-text"><i
                                                            class="fa fa-calendar-o"></i></span></span>
                                            </div>
                                        </section>
                                        <section class="col-3">
                                            <label>{{ __('Preview hour') }}</label>
                                            <div class="input-group preview_hour">
                                                <input type="text" class="form-control" value="{{ $previewHour }}">
                                                <span class="input-group-append"><span class="input-group-text"><i
                                                            class="fa fa-clock-o"></i></span></span>
                                            </div>
                                        </section>
                                        <section class="col-3">
                                            <label>{{ __('Scheduled date') }}</label>
                                            <div class="input-group">
                                                <input type="text" name="scheduled_date" id="scheduled_date"
                                                    class="form-control" @if($taskToUpdate->scheduled_date) value="{{
                                                $taskToUpdate->scheduled_date }}"@endif>
                                                <span class="input-group-append"><span class="input-group-text"><i
                                                            class="fa fa-calendar-o"></i></span></span>
                                            </div>
                                        </section>
                                        <section class="col-3">
                                            <label>{{ __('Scheduled hour') }}</label>
                                            <div class="input-group scheduled_hour">
                                                <input type="text" class="form-control" value="{{ $scheduledHour }}">
                                                <span class="input-group-append"><span class="input-group-text"><i
                                                            class="fa fa-clock-o"></i></span></span>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="form-group row">
                                        <section class="col">
                                            <label>{{ __('Additional notes') }}</label>
                                            <textarea name="taskAdditionalDescription" class="form-control"
                                                id="taskAdditionalDescription" wire:model.defer="taskAdditionalDescription"
                                                rows="4">
@if($taskToUpdate->additional_description != '') {{ $taskToUpdate->additional_description }} @endif
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
    <div class="card">
        <div class="card-footer justify-content-between">
            <div class="row">
                <div class="col text-right">
                    <a wire:click="cancel" class="btn btn-secondary mr-2">
                        {!! $cancelButton !!}
                    </a>
                    <a wire:click="saveTask" class="btn btn-primary">
                        {{ $actionButton }}
                        <span class="btn-icon-right"><i class="las la-check mr-2"></i></span>
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
            jQuery('#selectedCustomer').select2();
        });

        function restartObjects()
        {
            jQuery('#selectedLocation').select2();
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

        window.addEventListener('contentChanged', event => {
            restartObjects();
        });

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
