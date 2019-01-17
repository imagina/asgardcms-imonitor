@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ $product->title }} | @parent
@stop
@section('content')
<style>
    #toast-container>div
    {
        padding: 10px 10px 10px 45px;
        background-position: 10px center;
    }
    #toast-container a
    {
        font-weight: bold;
        text-decoration: underline;
    }
    .toast-message
    {
        font-family: sans-serif;
        line-height: 1;
    }
    .toast-message span.value
    {
        font-weight: bold;
    }
    #collapseHighcharts{
        background-color: #dddddd;
        padding-top: 10px;
        padding-bottom: 10px;
    }
    #collapseHighcharts .char{
    {
        margin-bottom: 10px;
    }
</style>
    <div id="content_show_imonitor" class="contaniner-imonitor">
        <div class="container">
            <!-- breadcrumb -->
                @component('imonitor::frontend.widgets.breadcrumb')
                    <li class="breadcrumb-item"><a href="{{ url('/monitor') }}"><span class="text-dark">Monitor</span></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->title }} </li>
                @endcomponent
            <!-- END-breadcrumb -->

            <!-- TITLE -->
            <div class="row">
                <div class="col-12 title text-dark text-left mb-5">
                    <div class="sub text-primary"> {{$product->title}} </div>
                    <div class="line mt-2 bg-secundary"></div>
                </div>
            </div>
            <!-- END-TITLE -->

            <!-- GRAFICA_PRODUCT -->
            <div class="row">
                <div class="col-12">
                    <div class="progress" v-if="loading" style="height: 3px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated w-100" role="progressbar"
                             aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div id="highcharts" v-bind:class="{ 'load-vue': loading }"></div>
                    <hr>
                </div>
                <button class="btn mb-2" type="button" data-toggle="collapse" data-target="#collapseHighcharts"
                        aria-expanded="false" aria-controls="collapseHighcharts">
                    VER GRÁFICOS INDIVIDUALES
                </button>
                <a href="{{ route('imonitor.product.unique',$product->id) }}" class="btn mb-2" type="button" data-toggle="collapse" data-target="#collapseHighcharts" aria-expanded="false" aria-controls="collapseHighcharts">
                    VER EN VENTANA NUEVA
                </a>
            </div>
            <!-- END-GRAFICA_PRODUCT -->

            <!-- GRAFICA_POR_SERIES_DEL_PRODUCT -->
            <div class="row collapse" id="collapseHighcharts"></div>
            <!-- END-GRAFICA_POR_SERIES_DEL_PRODUCT -->

            <div class="row">
                <div class="col-md-8 col-sm-12">
                    <div class="h4 mt-5">
                        {!! $product->description !!}
                    </div>
                    <a class="btn btn-secondary p-1 ml-1" href="{{ url('monitor/'.$product->id.'/historic') }}"
                       data-toggle="tooltip" data-placement="top" title="Histórico">
                        <i class="fa fa-history text-white" aria-hidden="true"></i>
                        <span class="d-none d-md-inline-block text-white">Ir al histórico</span>
                    </a>
                </div>
                <div class="col-md-4 col-sm-12">
                    @if(isset($product->address)&&!empty($product->address))
                        @php
                            $address=json_decode($product->address)
                        @endphp
                        <div class="map bg-light">
                            <div class="content mt-5">
                                <div id="map_canvas" style="width:100%; height:314px"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('imonitor::frontend.widgets.variables')
    {{-- <button onclick="prueba_notificacion()"> notificacion</button> --}}
@stop

@section('scripts')
    @parent
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script>
        var notificacion = {{ Auth::user()->hasAccess('imonitor.alerts.index') }};

            /*
            The purpose of this demo is to demonstrate how multiple charts on the same page
            can be linked through DOM and Highcharts events and API methods. It takes a
            standard Highcharts config with a small variation for each data set, and a
            mouse/touch event handler to bind the charts together.
            */

            /**
             * In order to synchronize tooltips and crosshairs, override the
             * built-in events with handlers defined on the parent element.
             */
        $(document).ready(function () {
            ['mousemove', 'touchmove', 'touchstart'].forEach(function (eventType) {
                document.getElementById('collapseHighcharts').addEventListener(
                    eventType,
                    function (e) {
                        var chart,point,i,event;

                        for (i = 0; i < Highcharts.charts.length; i = i + 1) {
                            chart = Highcharts.charts[i];
                            // Find coordinates within the chart
                            event = chart.pointer.normalize(e);
                            // Get the hovered point
                            point = chart.series[0].searchPoint(event, true);

                            if (point) {
                                point.highlight(e);
                            }
                        }
                    }
                );
            });

            /**
             * Override the reset function, we don't need to hide the tooltips and
             * crosshairs.
             */
            Highcharts.Pointer.prototype.reset = function () {
                return undefined;
            };

            /**
             * Highlight a point by showing tooltip, setting hover state and draw crosshair
             */
            Highcharts.Point.prototype.highlight = function (event) {
                event = this.series.chart.pointer.normalize(event);
                this.onMouseOver(); // Show the hover marker
                this.series.chart.tooltip.refresh(this); // Show the tooltip
                this.series.chart.xAxis[0].drawCrosshair(event, this); // Show the crosshair
            };
        });
        /**
         * Synchronize zooming through the setExtremes event handler.
         */
        function syncExtremes(e) {
            var thisChart = this.chart;
            if (e.trigger !== 'syncExtremes') { // Prevent feedback loop
                Highcharts.each(Highcharts.charts, function (chart) {
                    if (chart !== thisChart) {
                        if (chart.xAxis[0].setExtremes) { // It is null while updating
                            chart.xAxis[0].setExtremes(
                                e.min,
                                e.max,
                                undefined,
                                false,
                                { trigger: 'syncExtremes' }
                            );
                        }
                    }
                });
            }
        }

        toastr.options = toastr_options;
        var baseUrl = "{{ url('monitor/'.$product->id.'/historic') }}";

        const historial = new Vue({
            el: "#content_show_imonitor",
            data: {
                loading: true,
                series: {!! json_encode($product->variables) !!},
                product_id: {{ $product->id }},
                dataChart: {
                    data: [],
                    title: '{{$product->title}}'
                },
                seriesChart: []
            },
            mounted() {
                this.getDateChart();
                $('.load-span').addClass('load-hide');
            },
            methods: {
                getDateChart: function () {
                    this.renderChart();
                    Echo.channel('record-' + this.product_id)
                        .listen('.newRecord', (message) => {
                            this.pushRecord(message[0]);
                        });
                },
                renderChart: function () {
                    this.series.forEach((element, index) => {

                        this.dataChart.data.push({'name': element.title, 'data': [], 'id': element.id, 'chart': null});

                        this.seriesChart.push({'name': element.title, 'data': [], 'id': element.id, 'chart': null});

                        var chartContainer = document.createElement('div');

                        chartContainer.className = 'chart col-12';

                        document.getElementById('collapseHighcharts').appendChild(chartContainer);

                        subtitle = "max:"+this.series[index].pivot.max_value + "| min:"+this.series[index].pivot.min_value;

                        // LOS LIMITES DEL EJEX Y
                        var plotLines = [{
                            value: this.series[index].pivot.min_value,
                            color: 'red',
                            dashStyle: 'shortdash',
                            width: 1,
                            zIndex: 3,
                            label: { text: 'Valor mínimo: '+this.series[index].pivot.min_value }
                        }, {
                            value: this.series[index].pivot.max_value,
                            color: 'red',
                            dashStyle: 'shortdash',
                            width: 1,
                            zIndex: 3,
                            label: { text: 'Valor máximo:'+this.series[index].pivot.min_value }
                        }]

                        // GENERAR LA GRAFICA DE LA SERIE
                        this.seriesChart[index].chart = this.Highcharts(element.title,
                                                                        subtitle,
                                                                        chartContainer,
                                                                        [{ name: element.title, id: element.id, data: [] }],
                                                                        plotLines
                                                                    );
                    });

                    this.dataChart.chart = this.Highcharts(this.dataChart.title, null, 'highcharts',this.dataChart.data,null);

                    this.loading = false;
                },
                pushRecord: function (record) {
                    var length = this.dataChart.chart.series[0].data.length,
                        shift = length > 51,
                        created_at = +moment(record.created_at);

                    this.dataChart.data.forEach((value, index) => {
                        if (value.id == record.variable_id)
                        {

                            time = this.formatDatetime(record.created_at);

                            var newPoint = [time, parseFloat(record.value)];

                            this.dataChart.chart.series[index].addPoint(newPoint, true, shift);

                            this.seriesChart[index].chart.series[0].addPoint(newPoint, true, shift);

                            if (parseFloat(record.value) > this.series[index].pivot.max_value || parseFloat(record.value) < this.series[index].pivot.min_value) {

                                length = this.dataChart.chart.series[index].points.length-1;

                                var point = this.dataChart.chart.series[index].points[length];

                                length = this.seriesChart[index].chart.series[0].points.length-1;

                                var pointSerie = this.seriesChart[index].chart.series[0].points[length];

                                var type = parseFloat(record.value) > this.series[index].pivot.max_value ? 'máximo' : 'mínimo';
                                if(notificacion)
                                {
                                    toastr.error('Linea <span class="value">' + this.series[index].title + '</span> arrojo un valor '+type+' de <span class="value">' + parseFloat(record.value)+'</span><a href="'+baseUrl+'?alert='+record.created_at+'" class="d-block">Ir al historial</a>');
                                }

                                point.update(optionAlert);
                                pointSerie.update(optionAlert);
                            }
                        }
                    });
                },
                Highcharts: function (title,subtitle,chartContainer,series,plotLines)
                {
                    return Highcharts.chart(chartContainer, {
                        chart: {
                            type: 'spline',
                            animation: Highcharts.svg, // don't animate in old IE
                        },
                        title: {text: title},
                        subtitle: {
                            text: subtitle
                        },
                        xAxis: {
                            crosshair: true,
                            events: {
                                setExtremes: syncExtremes
                            },
                            tickAmount: 24,
                            type: 'datetime',
                            dateTimeLabelFormats:
                            {
                                second: '%H:%M:%S',
                                minute: '%H:%M',
                                hour: '%H:%M',
                                month: '%b \'%y',
                                day: '%e. %b',
                                year: '%Y',
                            },
                            labels: {
                                formatter: function()
                                {
                                    return moment(new Date(this.value)).format('DD/MM/YY HH:mm:ss');
                                },
                            },
                            shared: true
                        },
                        tooltip: {
                            formatter: function () {
                                return '<b>'+this.series.name+'</b><br>valor: '+this.y+'<br><small>' + moment(new Date(this.x)).format('DD/MM/YY HH:mm:ss')+'</small>';
                            },
                        },
                        yAxis: {
                            title: {text: 'Valores'},
                            plotLines: plotLines,
                        },
                        series: series
                    });
                },
                formatDatetime: function (datetime)
                {
                    return Date.parse(new Date(datetime));
                }
            }
        });

        function prueba_notificacion() {
            if (Notification) {
                if (Notification.permission !== "granted") {
                    Notification.requestPermission()
                }
                var title = "Xitrus"
                var extra = {
                    icon: "http://xitrus.es/imgs/logo_claro.png",
                    body: "Notificación de prueba en Xitrus"
                }
                var noti = new Notification( title, extra)
                noti.onclick = {
                // Al hacer click
                }
                noti.onclose = {
                // Al cerrar
                }
                setTimeout( function() { noti.close() }, 10000)
            }
        }
    </script>

    @if(isset($product->address) && !empty($product->address))
        <script type='text/javascript' src="https://maps.googleapis.com/maps/api/js?key={{Setting::get('imonitor::apiMap')}}&extension=.js&output=embed"></script>
        <script type="text/javascript">
            var geocoder, map, marker,
                latitude  = {{$address->lattitude}},
                longitude = {{$address->longitude}};

            $(document).ready(function ()
            {
                var OLD = new google.maps.LatLng(latitude, longitude);
                var options = {
                    zoom: zoom,
                    center: OLD,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,// ROADMAP | SATELLITE | HYBRID | TERRAIN
                    styles: mapStyles
                };
                map = new google.maps.Map(document.getElementById("map_canvas"), options);
                geocoder = new google.maps.Geocoder();
                marker = new google.maps.Marker({
                    map: map,
                    draggable: false,
                    position: OLD
                });
            });
        </script>
    @endif
@stop