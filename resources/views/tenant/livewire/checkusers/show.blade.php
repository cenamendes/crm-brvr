<div>
    <li class="nav-item dropdown notification_dropdown pl-2">
        <a class="nav-link bell bell-link" href="javascript:void(0)" role="button"
            data-toggle="dropdown">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
               d="M19.75,15.67a6,6,0,1,0-7.51,0A11,11,0,0,0,5,26v1H27V26A11,11,0,0,0,19.75,15.67ZM12,11a4,4,0,1,1,4,4A4,4,0,0,1,12,11ZM7.06,25a9,9,0,0,1,17.89,0Z"
                    fill="#326c91 " />
            </svg>
                {{-- <div id="alerticon" class="pulse-css" style="display:none;"></div> --}}
        </a>
        
        <!-- começa aqui -->
        <div class="chatbox" wire:ignore.self>
            <div class="chatbox-close"></div>
            <div class="custom-tab-1">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toogle="tab" href="#chat">Utilizadores</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="chat" role="tabpanel">
                        <div class="card mb-sm-3 mb-md-0 contacts_card">
                            <div class="card-body contacts_body p-0 dz-scroll ps ps--active-y" id="DZ_W_Contacts_Body">
                                <ul class="contacts">
                                    
                                    @foreach ($users as $key => $user )
    
                                        <li class="name-first-letter">{{$key}}</li>

                                        @foreach ($user as $us)
                                            
                                            <li class="active dz-chat-user">
                                                <div class="d-flex bd-highlight">
                                                    <div class="img_cont">
                                                        @if($us["photo"] == null)
                                                            <img src="{!! "https://".$_SERVER['SERVER_NAME']."/assets/resources/images/avatar/1.png" !!}" class="rounded-circle user_img" alt>
                                                        @else
                                                            <img src="{!! global_tenancy_asset('/app/public/profile/'.$us["photo"].'') !!}" class="rounded-circle user_img" alt>
                                                        @endif
                                                        @if($us["status"] == "online")
                                                            <span class="online_icon"></span>
                                                        @else
                                                            <span class="online_icon offline"></span>
                                                        @endif
                                                    </div>
                                                    <div class="user_info">
                                                        <span>{{$us["name"]}}</span>
                                                        @if($us["status"] == "online")
                                                            <p>online</p>
                                                        @else
                                                            @if($us["last_seen"] != "")
                                                                @php
                                                                    $difference = \Carbon\Carbon::parse($us["last_seen"])->addMinute(30)->diffForHumans();
                                                                @endphp
                                                                <p>offline {{$difference}}</p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endforeach

                                </ul>
                            </div>
                        </div>

                        <!--  Poderei fazer aqui a situação do chat  -->
                        <!-- Mas terei de fazer este codigo abaixo em livewire -->
                        {{-- <div class="card chat dz-chat-history-box d-none">
                            <div class="card-header chat-list-header text-center">
                                <a href="javascript:void(0)" class="dz-chat-history-back">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                            <rect fill="#000000" opacity="0.3" transform="translate(15.000000, 12.000000) scale(-1, 1) rotate(-90.000000) translate(-15.000000, -12.000000)" x="14" y="7" width="2" height="10" rx="1"></rect>
                                            <path d="M3.7071045,15.7071045 C3.3165802,16.0976288 2.68341522,16.0976288 2.29289093,15.7071045 C1.90236664,15.3165802 1.90236664,14.6834152 2.29289093,14.2928909 L8.29289093,8.29289093 C8.67146987,7.914312 9.28105631,7.90106637 9.67572234,8.26284357 L15.6757223,13.7628436 C16.0828413,14.136036 16.1103443,14.7686034 15.7371519,15.1757223 C15.3639594,15.5828413 14.7313921,15.6103443 14.3242731,15.2371519 L9.03007346,10.3841355 L3.7071045,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(9.000001, 11.999997) scale(-1, -1) rotate(90.000000) translate(-9.000001, -11.999997)"></path>
                                        </g>
                                    </svg>
                                </a>
                                <div>
                                    <h6 class="mb-1">Chat with Khele</h6>
                                    <p class="mb-0 text-success">Online</p>
                                </div>
                                <div class="dropdown"></div>
                            </div>
                            <div class="card-body msg_card_body dz-scroll ps ps--active-y" id="DZ_W_Contacts_Body3">
                                <div class="d-flex justify-content-start mb-4">
                                    <div class="img_cont_msg">
                                        <img src="https://acara.dexignzone.com/laravel/demo/images/avatar/1.jpg" class="rounded-circle user_img_msg" alt>
                                    </div>
                                    <div class="msg_cotainer">
                                        Ola, meu puto
                                        <span class="msg_time">8:40 AM, Today</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mb-4">
                                    <div class="msg_cotainer_send">
                                        Ya, ta tudo
                                        <span class="msg_time_send">9:40 AM, Today</span>
                                    </div>
                                    <div class="img_cont_msg">
                                        <img src="https://acara.dexignzone.com/laravel/demo/images/avatar/2.jpg" class="rounded-circle user_img_msg" alt>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer type_msg">
                                <div class="input-group">
                                    <textarea class="form-control" placeholder="Escreva a sua mensagem..."></textarea>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary">
                                            <i class="fa fa-location-arrow"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> --}}





                    </div>
                </div>
            </div>
        </div>


    </li>
    
@push('custom-scripts')
    <script>
         jQuery(document).ready(function(){
            

            //jQuery(".navbar-nav").on("click", ".bell-link", function(){
            jQuery(".bell-link").on('click',function() {
                Livewire.emit("RefreshUserLog");
            })
           // 

          
         })       
     
    </script>
@endpush
   
</div>

