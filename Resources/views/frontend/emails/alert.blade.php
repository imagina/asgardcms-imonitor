<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    @includeFirst(['emails.style', 'imonitor::frontend.emails.base.style'])

</head>

<body>
<div id="body">
    <div id="template-mail">

        @includeFirst(['emails.header', 'imonitor::frontend.emails.base.header'])

        @php
            $product=$alert->product;
            $record=$alert->record;
        @endphp

        {{-- ***** Order Content  ***** --}}
        <div id="contend-mail" class="p-3" style="text-align: center">

            {{-- ***** Title  ***** --}}
            <h3 class="text-center">
                Alerta del Producto <strong class="text-uppercase" style="color: #FA7F0E">{{$product->title}}</strong>
            </h3>

            <br>
            {{-- ***** URL  ***** --}}
            <p class="px-3">
                <strong>
                    Alerta generada a las:
                </strong>
                {{format_date($alert->created_at, '%A, %d %B de %Y %T') }}
            </p>
            <p class="px-3">
                <strong>
                   Variable:
                </strong>
               <span style="color: #FA7F0E">{{$record->variable->title}}</span>
            </p>
            <div style="margin-bottom: 5px">
                para la verificacion de esta alerta dar clic en el siguiete boton
                <br>

                <a class="btn btn-danger"  style="margin: 20px; color: #fff !important;"
                   href="{{url('monitor/'.$product->id.'/historic?alert='.$alert->created_at)}}">Ver alerta</a>
            </div>

            <div style="margin-top: 10px">
                Si el botón anterior no funciona, copia y pega la siguiente dirección en una ventana nueva del navegador.


                <p style="font-size:15px;line-height:18px;color:#666666;padding:0;margin:0;word-wrap:break-word;word-break:normal;font-size:12px;line-height:15px;color:#1428a0;padding:13px 16px 11px 16px;margin:10px 10% 21px 10%;background:#eef0f9;word-break:break-all">
                    <a href="{{url('monitor/'.$product->id.'/historic?alert='.$alert->created_at)}}" style="text-decoration:none;color:#1428a0;font-size:14px;line-height:15px;word-wrap:break-word;word-break:break-all" target="_blank">{{url('monitor/'.$product->id.'/historic?alert='.$alert->created_at)}}</a>
                </p>

            </div>

        </div>
        {{-- ***** End Order Content  ***** --}}


        @includeFirst(['emails.footer', 'imonitor::frontend.emails.base.footer'])


    </div>
</div>
</body>
</html>