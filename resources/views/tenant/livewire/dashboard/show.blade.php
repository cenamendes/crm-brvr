<div class="container-fluid">
<div class="row">
    <div class="col-xl-3">
      <div class="card">
        <div class="card-body">
          <h4 class="card-intro-title">{{__("Calendar")}}</h4>
          
          <div class="">
            <div>
              <p>{{ __("Team Members") }}</p>
              @foreach ( $teamMembers as $member )
                  <div class="col-xs-12 mb-2" style="padding-left:10px;">
                      <div class="row">
                      <div class="col-xs-3">
                          <span class="badge badge-primary rounded-circle" style="background:{{$member->color}}; padding:10px 10px!important;">
                              <label style="display:none;"></label>
                          </span>
                      </div>
                      <div class="col-xs-9">
                          <label>&nbsp;{{$member->name}}</label>
                      </div>
                  </div>
                  </div>
              @endforeach
            </div>
            <div class="events-tasks" style="display:none;">
              @foreach ($tasks as $task)
                @if($task->scheduled_date != null)
                    <span data-color="{{$task->tech->color}}" data-id="{{$task->id}}" data-scheduleddate="{{$task->scheduled_date}}" data-scheduledhour="{{$task->scheduled_hour}}">{{$task->taskCustomer->short_name}}</span>
                @else
                    <span data-color="{{$task->tech->color}}" data-id="{{$task->id}}" data-previewdate="{{$task->preview_date}}" data-previewhour="{{$task->preview_hour}}">{{$task->taskCustomer->short_name}}</span>
                @endif
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-9">
      <div class="row">
        <div class="col-xl-12">
          <div class="card">
            <div class="card-body">
    
              <div id="calendarr" class="app-fullcalendarr fc fc-unthemed fc-ltr" wire:ignore></div>
            </div>
          </div>
        </div>

        @if(Auth::user()->type_user != 2)

        <div class="col-xl-12 mt-4">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">{{ __("Notifications")}}</h4>
            </div>
            <div class="card-body" style="display:flex;overflow:auto;">
              <table id="dataTables-data" class="table table-responsive-lg mb-0 table-striped">
                <thead>
                  <tr>
                    <th>{{ __('Service') }}</th>
                    {{-- <th>{{ __('Date Final of service') }}</th> --}}
                    <th>{{ __('Technical') }}</th>
                    <th>{{ __('Customer') }}</th>
                    <th>{{ __('Customer Location') }}</th>                   
                    <th>{{ __('Notification day') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @if ($servicesNotifications != null)
                    @foreach ($servicesNotifications as $notification)
                      <tr>
                        <td>{{$notification["service"]}}</td>
                        {{-- <td>{{$notification["date_final_service"]}}</td> --}}
                        <td>{{$notification["team_member"]}}</td>
                        <td>{{$notification["customer"]}}</td>
                        <td>{{$notification["customer_county"]}}</td>
                        <td>{{$notification["notification"]}}</td>
                        <td>
                          <div class="d-flex">
                            <a href="javascript:void(0)" wire:click="treated({{$notification["customerServicesId"]}})" class="btn btn-primary btn-sm light px-4">{{__("Treated")}}</a>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>

        @endif
      
    </div>
    </div>
  </div>
</div>

