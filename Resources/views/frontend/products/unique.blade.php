@extends('layouts.master')

@section('title')
    {{ $product->title }} | @parent
@stop

@section('content')
	<style> .header-page,.menu-fixed,.imsocial,footer{ display: none } </style>

    <div id="content_show_imonitor" class="contaniner-imonitor">
        <div class="container">
            <!-- TITLE -->
                @component('imonitor::frontend.widgets.title')
                    <div class="sub text-primary"> {{$product->title}} </div>
                @endcomponent
            <!-- END-TITLE -->

            @if(isset($event) && !empty($event))
                @if(Auth::user()->hasAccess('imonitor.products.unique'))
                    <div class="row">
                        <div class="col-12 text-right">
                            <div class="py-2 badge {{ $event->present()->valueLabelClass }}" data-toggle="tooltip" data-placement="top" title="{{ $event->present()->valueLabel }}" id="badge-push-event">
                                <i class="fa fa-power-off" aria-hidden="true"></i>
                                <span class="text-uppercase">{{ $event->present()->valueLabel }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- GRAFICA_PRODUCT -->
            <div class="row">
                <div class="col-12">
                    <div class="progress" v-if="loading" style="height: 3px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated w-100" role="progressbar"
                             aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div id="highcharts" v-bind:class="{ 'load-vue': loading }"></div>
                </div>
                <div class="col-12 border-bottom border-top py-2">
                    <button class="btn btn-secondary ml-1" type="button" data-toggle="collapse" data-target="#collapseHighcharts"
                            aria-expanded="false" aria-controls="collapseHighcharts">
                        VER GRÁFICOS INDIVIDUALES
                    </button>
                    <a class="btn btn-primary ml-1" href="{{route('imonitor.product.historic',$product->id)}}" data-toggle="tooltip" data-placement="top" title="Grafica tiempo Rear">
                        <i class="fa fa-area-chart text-white" aria-hidden="true"></i>
                        <span class="d-none d-md-inline-block text-white">Ver hitorial</span>
                    </a>
                    @if(Auth::user()->hasAccess('imonitor.alerts.index') && count($product->alersatives) > 0)
                        <a class="btn btn-danger ml-1" href="{{route('imonitor.alerts.product',$product->id)}}" data-toggle="tooltip" data-placement="top" title="Ver las alertas del product">
                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                            <span class="d-none d-md-inline-block text-white">
                                {{count($product->alersatives)}}
                            </span>
                        </a>
                    @endif
                    @if(Auth::user()->hasAccess('imonitor.products.unique'))
                        <button class="btn btn-danger ml-1" v-on:click="sendMensaje()" :disabled="loadingMsg">
                            <i class="fa fa-commenting-o" aria-hidden="true"></i>
                            <span class="d-none d-md-inline-block">Mensaje</span>
                        </button>
                    @endif
	           	</div>
            </div>
            <!-- END-GRAFICA_PRODUCT -->

            <!-- GRAFICA_POR_SERIES_DEL_PRODUCT -->
            <div class="row collapse" id="collapseHighcharts"></div>
            <!-- END-GRAFICA_POR_SERIES_DEL_PRODUCT -->

        </div>
    </div>
    @include('imonitor::frontend.widgets.variables')
@endsection

@section('scripts')
    @parent
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>

        var notificacion = 0;
        @if (Auth::check())
            @if(Auth::user()->hasAccess('imonitor.alerts.index'))
                notificacion = 1;
            @endif
        @endif

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
        const historial = new Vue({
            el: "#content_show_imonitor",
            data: {
                loading: true,
                loadingMsg: false,
                maintenance: {{ $product->maintenance }},
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

                    Echo.channel('product-' + this.product_id)
                        .listen('.product', (message) => {
                            this.pushProduct(message[0]);
                        });

                    Echo.channel('event-' + this.product_id)
                        .listen('.newEvent', (message) => {
                            this.pushEvent(message[0]);
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
                            [{name: element.title, id: element.id, data: []}],
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

                                if (notificacion && !this.maintenance) {
                                    toastr.error('Linea <span class="value">' + this.series[index].title + '</span> arrojo un valor ' + type + ' de <span class="value">' + parseFloat(record.value) + '</span><a href="' + route_historic + '?alert=' + record.created_at + '" class="d-block">Ir al historial</a>');
                                }

                                point.update(optionAlert);
                                pointSerie.update(optionAlert);
                            }
                        }
                    });
                },
                pushProduct: function(product) {
                    this.maintenance = product.maintenance;
                },
                pushEvent: function(event) {
                    var badge = $('#badge-push-event');
                    console.log(event);
                    if(badge.hasClass('bg-green'))
                        badge.removeClass('bg-green').addClass('bg-red').find('span').text('APAGADO');
                    else
                        badge.removeClass('bg-red').addClass('bg-green').find('span').text('ENCENDIDO');
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
                },
                sendMensaje: function () {
                    this.loadingMsg = true;
                    axios.post('{{ route('imonitor.product.alert.client', $product->id) }}')
                        .then(response => {
                            console.log(response);
                            if (response.data.status != "error")
                                toastr.success('Mensaje enviado', {timeOut: 5000});
                            else
                                toastr.error('Por favor intente más tarde', 'Ocurrio algo!', {timeOut: 5000});
                        }).finally(() => {
                        this.loadingMsg = false;
                    })
                }
            }
        });

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>

    <style>
        .badge {
            font-size: 19px;
            cursor: default;
        }

        .bg-red {
            color: #fff;
            background-color: #dc3545;
        }
        .bg-red .fa-power-off
        {
            transform: rotate(180deg);
        }

        .bg-green {
            color: #fff;
            background-color: #28a745;
        }
    </style>
@stop