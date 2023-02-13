<x-tenant-layout title="{{ __('Dashboard') }}" :themeAction="$themeAction">
    <div class="container-fluid">
        <div class="row">
            {{-- <div class="container-fluid"> --}}
                <!-- Add Order -->
                <div class="modal fade" id="addOrderModalside">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Add Event</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <form>
                          <div class="form-group">
                            <label class="text-black font-w500">Event Name</label>
                            <input type="text" class="form-control">
                          </div>
                          <div class="form-group">
                            <label class="text-black font-w500">Event Date</label>
                            <input type="date" class="form-control">
                          </div>
                          <div class="form-group">
                            <label class="text-black font-w500">Description</label>
                            <input type="text" class="form-control">
                          </div>
                          <div class="form-group">
                            <button type="button" class="btn btn-primary">Create</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- row -->
                
                @livewire('tenant.dashboard.show')
                
              {{-- </div> --}}
        </div>
    </div>
</x-tenant-layout>

{{-- @push('custom-scripts') --}}
<script>
  var mesAtual = (new Date).getMonth() + 1;
  var anoAtual = (new Date).getFullYear();
  var date = (new Date);
 
  document.addEventListener('livewire:load', function () {
      restartCalendar();
          
  });



    jQuery('body').on('click','.fc-next-button',function(){
   
      Livewire.emit('CalendarNextChanges',date);

    });

    jQuery('body').on('click','.fc-prev-button',function(){

      Livewire.emit('CalendarPreviousChanges',date);

    });

    function restartCalendar()
    {
     
      var event = [];
    var startDate = "";
    var titleTask = "";
  
    jQuery('.events-tasks span').each(function(){
      if(jQuery(this).attr("data-previewdate") != "" && typeof jQuery(this).attr("data-previewdate") !== 'undefined')
      {
        titleTask = "ℹ "+jQuery(this).text();
        startDate = jQuery(this).attr("data-previewdate");
        totalHour = startDate+"T"+jQuery(this).attr("data-previewhour");
        color = jQuery(this).attr("data-color");
      }
      if(jQuery(this).attr("data-scheduleddate") != "" && typeof jQuery(this).attr("data-scheduleddate") !== 'undefined') 
      {
        titleTask = jQuery(this).text();
        startDate = jQuery(this).attr("data-scheduleddate");
        totalHour = startDate+"T"+jQuery(this).attr("data-scheduledhour");
        color = jQuery(this).attr("data-color");
      }

        {event.push({
            title: titleTask,
            start: totalHour,
            color: color
        })}
    });

      
    jQuery('#calendarr').fullCalendar({
      defaultView: 'month',
      events:event,
      header: {
             center:'title',
             left:'prev,next,today',
             right:'month'
         },
         buttonText: {
          today: 'Hoje',
          day: 'Dia',
          week:'Semana',
          month:'Mês'
        },
        slotLabelFormat: [
          'ddd D/M',
          'H:mm'
        ],
        allDayText: "Horas",
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'],
        timeFormat: 'HH(:mm)',
        aspectRatio:  2 
    });
   
    
   


  }
  
    window.addEventListener('calendar',function(e){
      console.log(e);
      jQuery('#calendarr').fullCalendar('destroy');
      restartCalendar();
      jQuery('#calendarr').fullCalendar('gotoDate',e.detail);
      });

        
   
    
</script>   
{{-- @endpush --}}
