<div>
    <div style="clear:both; position:relative;">
        <div style="position:absolute; left:0pt; width:192pt;">
            <table style="width: 100%;font-size:25px;">
                
                    <tr style="margin-left:20px;">
                        {{-- <td style="text-align:center;">{!! DNS1D::getBarcodeHTML($impressao["barcode"], "C128",2.7,50) !!}</td> --}}
                        <td><img src="https://suporte.brvr.pt/cl/7f3a1b73-d8ae-464f-b91e-2a3f8163bdfb/app/public/images/logo/brvr_original.png"  width="60"></td>
                    </tr>
                    <tr>
                        <td style="display: none;">TESTE PARA ENCHER ESPAÇO</td>
                    </tr>
                    <tr style="width:100%;margin-top:20px;">
                        <td style="font-size:12px;">Tarefa Nº {{$impressao->taskReference}}</td>
                    </tr>   
                    <tr>
                        <td style="font-size:12px;">{{ date('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:12px;">{{ $impressao->customer->short_name }}</td>
                    </tr>
            
            </table>
        </div>
        <div style="margin-left:100pt;">
            <table style="width: 100%;font-size:25px;">
                <tr style="text-align:left;">
                    <td><img src="data:image/png;base64, {!! $qrcode !!}"></td>
                </tr>
            </table>
        </div>
    </div>
</div>