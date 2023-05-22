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
            <a class="nav-link {{ $homePanel }}" data-toggle="tab" href="#homePanel">
                <i class="la la-home mr-2"></i> {{ __('Customer') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $servicesPanel }}" data-toggle="tab" href="#servicesPanel">
                <i class="flaticon-381-notepad mr-2"></i> {{ __('Service') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $reportPanel }}" data-toggle="tab" href="#reportPanel">
                <i class="la la-home mr-2"></i> {{ __('Report') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $timesPanel }}" data-toggle="tab" href="#timesPanel">
                <i class="la la-hourglass-end mr-2"></i> {{ __('Times') }}
            </a>
        </li>
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
                                                    <option value="{{ $task->taskCustomer->id }}" selected>{{
                                                        $task->taskCustomer->name }}</option>
                                                </select>
                                            </section>
                                        </div>
                                        <div class="row form-group">
                                            <section class="col-3">
                                                <label>{{ __('VAT') }}</label>
                                                <input type="text" name="vat" id="vat" class="form-control"
                                                    value="{{ $task->taskCustomer->vat }}" readonly>
                                            </section>
                                            <section class="col-3">
                                                <label>{{ __('Phone number') }}</label>
                                                <input type="text" name="phone" id="phone" class="form-control"
                                                    value="{{ $task->taskCustomer->contact }}" readonly>
                                            </section>
                                            <section class="col-6">
                                                <label>{{ __('Primary e-mail address') }}</label>
                                                <input type="text" name="email" id="email" class="form-control"
                                                    value="{{ $task->taskCustomer->email }}" readonly>
                                            </section>
                                        </div>
                                        <div class="row form-group">
                                            <section class="col-12">
                                                <label>{{ __('Customer Address') }}</label>
                                                <input type="text" name="address" id="address" class="form-control"
                                                    value="{{ $task->taskCustomer->address }}" readonly>
                                            </section>
                                        </div>
                                        <div class="row form-group">
                                            <section class="col-2">
                                                <label>{{ __('Zip Code') }}</label>
                                                <input type="text" name="zipcode" id="zipcode" class="form-control"
                                                    value="{{ $task->taskCustomer->zipcode }}" readonly>
                                            </section>
                                            <section class="col-5">
                                                <label>{{ __('District') }}</label>
                                                <input type="text" name="district" id="district" class="form-control"
                                                    value="{{ $task->taskCustomer->customerDistrict->name }}"
                                                    readonly>
                                            </section>
                                            <section class="col-5">
                                                <label>{{ __('County') }}</label>
                                                <input type="text" name="zipcode" id="zipcode" class="form-control"
                                                    value="{{ $task->taskCustomer->customerCounty->name }}"
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
        <div class="tab-pane fade {{ $servicesPanel }}" id="servicesPanel" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-0" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row form-group">
                                    <div class="col">
                                        <label>{{ __('Location') }}</label>
                                        <select name="selectedLocation" id="selectedLocation" disabled>
                                            <option value="{{ $task->location_id }}" selected>
                                                {{ $task->taskLocation->description }} | {{ $task->taskLocation->locationCounty->name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                @if(isset($numberOfSelectedServices) && $numberOfSelectedServices > 0)
                                    @for($i=0; $i < $numberOfSelectedServices; $i++)
                                        <div class="row form-group">
                                            <div class="col">
                                                <label>{{ __('Service Name') }}</label>
                                                <select name="selectedService_{{ $i }}" id="selectedService_{{ $i }}"
                                                    class="selectedService" data-rel="{{ $i }}" disabled>
                                                    <option value="">{{ __('Select Service') }}</option>
                                                    @forelse ($customerServicesList as $item)
                                                    <option value="{{ $item->service->id }}"
                                                        @if(isset($selectedServiceId[$i]) && $selectedServiceId[$i] == $item->service->id) selected
                                                        @endif>{{ $item->service->name }}</option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col">
                                                <label>{{ __('Service description or notes ') }}</label>
                                                <textarea name="serviceDescription_{{ $i }}"
                                                    class="form-control serviceDesription" id="serviceDescription_{{ $i }}"
                                                    wire:model.defer="serviceDescription.{{ $i }}" data-rel="{{ $i }}"
                                                    rows="4" readonly>
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
        <div class="tab-pane fade {{ $reportPanel }}" id="reportPanel" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-0" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="col">
                                        <div class="row form-group">
                                            <section class="col-12 form-group">
                                                <label>{{ __('Additional notes') }}</label>
                                                <textarea class="form-control" rows="4" cols="50" name="notes" id="notes" disabled>
{{ $task->additional_description }}
                                                </textarea>
                                            </section>
                                            <section class="col-12 form-group">
                                                <label>{{ __('Report') }}</label>
                                                <textarea class="form-control" wire:model.defer="report" rows="4" cols="50" name="report" id="report" @if(Auth::user()->type_user == 2) disabled @endif></textarea>
                                            </section>
                                            <section class="col-12 form-group">
                                                <label>{{ __('Conclusion') }}</label>
                                                <textarea class="form-control" rows="4" cols="50" name="conclusion" id="conclusion" wire:model.defer="conclusion" @if(Auth::user()->type_user == 2) disabled @endif></textarea>
                                            </section>
                                            <section class="col-12 form-group">
                                                <label>{{ __('Confidential Information') }}</label>
                                                <textarea class="form-control" rows="4" cols="50" name="confidential_information" id="confidential_information" wire:model.defer="confidential_information" @if(Auth::user()->type_user == 2) disabled @endif></textarea>
                                            </section>
                                            <section class="col-12 form-group">
                                                <input type="text" id="concluded" wire:model.defer="concluded" style="display:none;">
                                                <p class="informationConclusion"  style="display:none;">
                                                    <label>{{ __('Information Conclusion') }}</label>
                                                    <textarea  class="form-control" rows="4" cols="50" name="infoConcluded" id="infoConcluded" wire:model.defer="infoConcluded" @if(Auth::user()->type_user == 2) disabled @endif></textarea>
                                                </p>
                                                <p>
                                                    <label>{{ __('State of Task') }}</label>
                                                    {{-- <div class="btn-group">
                                                        <button id="concludedButton" class="btn btn-primary" style="background-color:gray">{{__('Concluded')}}</button>
                                                        <button id="notConcludedButton" class="btn btn-primary" style="background-color:gray;">{{__('Not Concluded')}}</button>
                                                    </div> --}}
                                                    <div class="form-group">
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" id="concludedButton" class="form-check-input" @if(Auth::user()->type_user == 2) disabled @endif>
                                                                {{__('Concluded')}}
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" id="notConcludedButton" class="form-check-input" @if(Auth::user()->type_user == 2) disabled @endif>
                                                                {{__('Not Concluded')}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </p>
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
        <div class="tab-pane fade {{ $timesPanel }}" id="timesPanel" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-0" style="border-top-left-radius: 0px; border-top-right-radius: 0px;">
                        <div class="card-body">
                            @livewire('tenant.tasks-times.show-times',['task' => $task])
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
                    @if(Auth::user()->type_user != 2)
                        <a wire:click="updateTaskReport" class="btn btn-primary">
                            {{ __("Update Task Report") }}
                            <span class="btn-icon-right"><i class="las la-check mr-2"></i></span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('custom-scripts')
    <script>
        var services = [];
        document.addEventListener('livewire:load', function () {
            restartObjects();
            checkConclusion();
            jQuery('#selectedCustomer').select2();
        });

        function restartObjects()
        {
            jQuery('#selectedLocation').select2();

            jQuery('.selectedService').each(function() {
                var selectId = '#' + jQuery(this).attr('id');
                jQuery(selectId).off('select2:select');
                jQuery(selectId).select2();
            });
        }

        window.addEventListener('loading', function(e) {
            @this.loading();
           
        });

        window.addEventListener('reportMessage',function(e){
  
            swal.fire({
                title: "Report",
                html: "Need to insert a commentary if not concluded",
                type: "error",
                confirmButtonColor: '#d33'})

                checkConclusion();

                restartObjects();

        });

        window.addEventListener('swal',function(e){
             Swal.fire({
                title: e.detail.title,
                html: e.detail.message,
                type: e.detail.status,

            })  
            
            checkConclusion();
            
        });
        


        window.addEventListener('newService', function(e) {
            jQuery('.selectedService').each(function() {
                var selectId = '#' + jQuery(this).attr('id');
                jQuery(selectId).off('select2:select');
                jQuery(selectId).select2();
            });
        });

        window.addEventListener('contentChanged', event => {
            restartObjects();
        });

        function checkConclusion()
        {
            if (jQuery("#concluded").val() == 0) {
                jQuery("#notConcludedButton").attr("checked",true);
                jQuery("#concludedButton").attr("checked",false);
                jQuery(".informationConclusion").css("display","block");
            }
            else if(jQuery("#concluded").val() == 1) {
                jQuery("#concludedButton").attr("checked",true);
                jQuery("#notConcludedButton").attr("checked",false);
                jQuery(".informationConclusion").css("display","none");
            }
        }

        jQuery( document ).ready(function() {
           checkConclusion();
        });

        jQuery("body").on("click","#concludedButton",function(){
           
                jQuery("#concludedButton").prop('checked', true);
                jQuery("#notConcludedButton").prop('checked', false);
                jQuery(".informationConclusion").css("display","none");
                @this.set('concluded',1,true);
            
        });

        jQuery("body").on("click","#notConcludedButton",function(){
           
                jQuery("#notConcludedButton").prop('checked', true);
                jQuery("#concludedButton").prop('checked', false);
                jQuery(".informationConclusion").css("display","block");
                @this.set('concluded',0,true);
            
        });

      

    </script>
    @endpush
</div>
