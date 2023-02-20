<div>
<style>
        /* Chat containers */
.cont {
  border: 2px solid #dedede;
  background-color: #f1f1f1;
  border-radius: 30px;
  padding: 10px;
  margin: 10px 0;
}

/* Darker chat container */
.darker {
  border-color: #ccc;
  background-color: #ddd;
}

/* Clear floats */
.cont::after {
  content: "";
  clear: both;
  display: table;
}

/* Style images */
.cont img {
  float: left;
  max-width: 60px;
  width: 100%;
  margin-right: 20px;
  border-radius: 20px;
}

/* Style the right image */
.cont img.right {
  float: right;
  margin-left: 20px;
  margin-right:0;
}

/* Style time text */
.time-right {
  float: right;
  color: #aaa;
}

/* Style time text */
.time-left {
  float: left;
  color: #999;
}
</style>

    <div id="wrapper">
        <div id="menu">
            <p class="welcome">Olá, {{Auth::user()->name}}<b></b></p>
        </div>
        <div id="chatbox" style="max-height:400px; border:1px solid #326c91 ; overflow:scroll; overflow-x: hidden;">
          <div>
            @if ($chat->isEmpty())
            <div class="container">
                <h4 class="text-center">Escreva uma mensagem para começar conversação</h4>
            </div>
            @endif
            @foreach ($chat as $ch )
            @php
                $name = \App\Models\User::where('id',$ch->user_id)->first();
            @endphp
            @if($ch->user_id == Auth::user()->id)
                <div class="container cont darker" style="width:50%;margin-right:0;margin-left:auto;margin-top:0;">
                    @if(Auth::user()->photo != null)
                      <img src="{!! global_tenancy_asset('/profile/'.Auth::user()->photo.'') !!}" width="20" alt="" class="right">
                    @else
                      <img src="{!! global_asset('assets/resources/images/avatar/1.png') !!}" width="20" alt="" class="right">
                    @endif
                    {{-- <p style="color:#326c91 ;text-align:right;">{{$name->name}}</p> --}}
                    <div class="row"><div class="col-xl-6 text-left pl-0"><span class="time-left">{{$ch->created_at}}</span></div><div class="col-xl-6 pr-0"><p style="color:#326c91 ;text-align:right;">{{$name->name}}</p></div></div>
                    <p style="text-align:right;width:95%;line-height:1.2;margin-bottom:0;">{{$ch->message}}</p>
                    {{-- <span class="time-left">{{$ch->created_at}}</span> --}}
                </div>
            @else
                <div class="container cont" style="width:50%;margin-top:0;">
                    @php
                        $photo = \App\Models\User::where('id',$ch->user_id)->first();
                    @endphp
                    @if($photo->photo != null)
                    <img src="{!! global_tenancy_asset('/profile/'.$photo->photo.'') !!}" width="20" alt="">
                    @else
                    <img src="{!! global_asset('assets/resources/images/avatar/1.png') !!}" width="20" alt="">
                    @endif
                    <div class="row"><div class="col-xl-6 pl-0"><p style="color:#326c91 ;">{{$name->name}}</p></div><div class="col-xl-6 text-right pr-0"><span class="time-right">{{$ch->created_at}}</span></div></div>
                    <p style="line-height:1.2;margin-bottom:0;">{{$ch->message}}</p>
                    {{-- <span class="time-right">{{$ch->created_at}}</span> --}}
                  </div>
            @endif
                   
                
               
            @endforeach
          </div>
        </div>
        <div class="row mt-2">
            <div class="col-xl-10">
                <input class="form-control" style="border:1px solid black; color:black;border-radius:5px;" name="usermsg" type="text" id="usermsg" wire:model="usermsg" />
            </div>
           <div class="col-xl-2 text-center" style="display:grid;">
                <button type="button" style="border-radius:5px;" class="btn btn-primary" name="submitmsg" id="submitmsg" wire:click="SendMessage">Envia &nbsp;<i class="fa fa-paper-plane" aria-hidden="true"></i></button>
           </div>
        </div>
    </div>


@if (Auth::user()->type_user == 2)
@push('custom-scripts')
@endif
      
   
<script>

jQuery(document).ready(function(){ 
  var input = document.getElementById("usermsg");
  //Livewire.emit("submitmsg")
  input.addEventListener("keypress", function(event) {
  // If the user presses the "Enter" key on the keyboard
  if (event.key === "Enter") {
    // Cancel the default action, if needed
    event.preventDefault();
    // Trigger the button element with a click
    document.getElementById("submitmsg").click();
  }
});


    jQuery('#chatbox').on('scroll',function(){
        clearInterval(time);
        time = setInterval(updateScroll, 10000000000000000);
    });

    updateScroll();

    function updateScroll() {
      var element = document.getElementById("chatbox");
      element.scrollTop = element.scrollHeight;
    }

    var time = setInterval(updateScroll, 100);
    
    window.addEventListener('refreshChatPosition',function(e){
      clearInterval(time);
      time = setInterval(updateScroll, 100);
    });

});
  

</script>
@if (Auth::user()->type_user == 2)
@endpush
@endif
</div>