@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ $product->title }} | @parent
@stop
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <div id="content_show_imonitor" class="my-5">
        <div class="container">
            <!-- breadcrumb -->
            <div class="row">
                <nav aria-label="breadcrumb" class="col-12 text-right">
                    <ol class="breadcrumb rounded-0 pull-right" style="background-color: transparent;">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}"><span class="text-dark">Inicio</span></a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ url('/monitor') }}"><span
                                        class="text-dark">Monitor</span></a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $product->title }} </li>
                    </ol>
                </nav>
            </div>
            <!-- END-breadcrumb -->

            <!-- TITLE -->
            <div class="row">
                <div class="col-12 title text-dark text-left mb-5">
                    <div class="sub text-primary"> {{$product->title}} </div>
                    <div class="line mt-2 bg-secundary"></div>
                </div>
            </div>
            <!-- END-TITLE -->

            <!-- PRODUCT -->
            <div class="row">
                <div class="col-12">
                    <div class="progress" v-if="loading" style="height: 3px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated w-100" role="progressbar"
                             aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div id="highcharts" v-bind:class="{ 'load-vue': loading }"></div>
                    <hr>
                    <button class="btn mb-2" type="button" data-toggle="collapse" data-target="#collapseHighcharts"
                            aria-expanded="false" aria-controls="collapseHighcharts">
                        VER GRÁFICOS INDIVIDUALES
                    </button>
                </div>
            </div>
            <!-- END-PRODUCTS -->
            <div class="row collapse" style="background: #80808026;border-radius: 10px;" id="collapseHighcharts">
                @foreach ($product->variables as $variable)
                    <div class="col-12">
                        <div id="highcharts_{{$variable->id}}" class="border-bottom border-primary my-2"></div>
                        <p>
                            <small>valor máximo: {{$variable->pivot->max_value}} | valor
                                minimo: {{$variable->pivot->min_value}}</small>
                        </p>
                    </div>
                @endforeach
            </div>

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
@stop

@section('scripts')
    @parent
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        var optionAlert = {
                marker: {
                    symbol: 'circle',
                    fillColor: "rgb(250, 14, 14)",
                    lineColor: "rgb(0, 0, 0)",
                    radius: 8
                }
            },
            zoom = 16;
        const historial = new Vue({
            el: "#content_show_imonitor",
            data: {
                loading: true,
                series: {!! json_encode($product->variables) !!},
                product_id: {{ $product->id }},
                dataChart: {
                    data: [],
                    chart: null,
                    title: '{{$product->title}}'
                },
                seriesChart: []
            },
            mounted() {
                this.getDateChart();
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
                        this.dataChart.data.push({'name': element.title, 'data': [], 'id': element.id});
                        this.seriesChart.push({'name': element.title, 'data': [], 'id': element.id, 'chart': null});
                        this.seriesChart[index].chart = Highcharts.chart('highcharts_' + element.id, {
                            chart: {
                                type: 'spline',
                                animation: Highcharts.svg, // don't animate in old IE
                            },
                            title: {text: element.title},
                            xAxis: {
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
                                    format: '{value: %e/%m/%y %H:%M:%S}'
                                },
                            },
                            yAxis: {title: {text: 'Valores'}},
                            series: [{
                                name: element.title,
                                id: element.id,
                                data: []
                            }]
                        });
                    });

                    this.dataChart.chart = Highcharts.chart('highcharts', {
                        chart: {
                            type: 'spline',
                            animation: Highcharts.svg, // don't animate in old IE
                        },
                        title: {text: this.dataChart.title},
                        xAxis: {
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
                                format: '{value: %e/%m/%y %H:%M:%S}'
                            },
                        },
                        yAxis: {title: {text: 'Valores'}},
                        series: this.dataChart.data,
                    });
                },
                pushRecord: function (record) {
                    var length = this.dataChart.chart.series[0].data.length;
                    var shift = length > 51;
                    var created_at = +moment(record.created_at);
                    this.dataChart.data.forEach((value, index) => {
                        if (value.id == record.variable_id) {

                            this.dataChart.chart.series[index].addPoint([+moment(), parseFloat(record.value)], true, shift);

                            this.seriesChart[index].chart.series[0].addPoint([+moment(), parseFloat(record.value)], true, shift);

                            if (parseFloat(record.value) > this.series[index].pivot.max_value || parseFloat(record.value) < this.series[index].pivot.min_value) {
                                var point = this.dataChart.chart.series[index].points[length - 1];
                                var pointSerie = this.seriesChart[index].chart.series[0].points[length - 1]
                                if (parseFloat(record.value) > this.series[index].pivot.max_value) {
                                    toastr.warning('Linea ' + this.series[index].title + ' arrojo un valor maximo de ' + parseFloat(record.value));
                                }
                                if (parseFloat(record.value) < this.series[index].pivot.min_value) {
                                    toastr.error('Linea ' + this.series[index].title + ' arrojo un valor minimo de ' + parseFloat(record.value));
                                }
                                point.update(optionAlert);
                                pointSerie.update(optionAlert);
                            }
                        }
                    });
                }
            }
        });
    </script>

    @if(isset($product->address)&&!empty($product->address))
        <script type='text/javascript'
                src="https://maps.googleapis.com/maps/api/js?key={{Setting::get('imonitor::apiMap')}}&extension=.js&output=embed"></script>
        <script type="text/javascript">
            var geocoder, map, marker,
                styles = [
                    {
                        "featureType": "administrative",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "color": "#a7a7a7"
                            }
                        ]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#737373"
                            }
                        ]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#ffffff"
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#dadada"
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "labels",
                        "stylers": [
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "labels.icon",
                        "stylers": [
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "geometry",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#ffa000"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#ffa000"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "geometry.stroke",
                        "stylers": [
                            {
                                "color": "#ffa000"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels",
                        "stylers": [
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.text",
                        "stylers": [
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#ffffff"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.text.stroke",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#ffa000"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "labels.icon",
                        "stylers": [
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#ffa000"
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "geometry.stroke",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#ffa000"
                            }
                        ]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#ffa000"
                            }
                        ]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "geometry.stroke",
                        "stylers": [
                            {
                                "color": "#ffa000"
                            }
                        ]
                    },
                    {
                        "featureType": "road.local",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "visibility": "on"
                            },
                            {
                                "color": "#ffcf7f"
                            },
                            {
                                "weight": 1.8
                            }
                        ]
                    },
                    {
                        "featureType": "road.local",
                        "elementType": "geometry.stroke",
                        "stylers": [
                            {
                                "color": "#ffa000"
                            }
                        ]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "all",
                        "stylers": [
                            {
                                "color": "#808080"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#d3d3d3"
                            }
                        ]
                    }
                ];

            function initialize() {
                var latitude ={{$address->lattitude}};
                var longitude ={{$address->longitude}};
                var OLD = new google.maps.LatLng(latitude, longitude);
                var options = {
                    zoom: zoom,
                    center: OLD,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,// ROADMAP | SATELLITE | HYBRID | TERRAIN
                    styles: styles
                };
                map = new google.maps.Map(document.getElementById("map_canvas"), options);
                geocoder = new google.maps.Geocoder();
                marker = new google.maps.Marker({
                    map: map,
                    draggable: false,
                    position: OLD
                });
            }

            $(document).ready(function () {
                initialize();
            });
        </script>
    @endif
    @if
@stop