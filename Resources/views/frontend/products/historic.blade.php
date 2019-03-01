@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ $product->title }} | @parent
@stop
@section('content')
    <div id="content_historic_imonitor" class="contaniner-imonitor">
		<div class="container">
			<!-- breadcrumb -->
				@component('imonitor::frontend.widgets.breadcrumb')
	    			<li class="breadcrumb-item"><a href="{{ url('/monitor') }}"><span class="text-dark">Monitor</span></a></li>
	    			<li class="breadcrumb-item active" aria-current="page">{{ $product->title }} | Historia </li>
				@endcomponent
			<!-- END-breadcrumb -->

            <!-- TITLE -->
            @component('imonitor::frontend.widgets.title')
                <span class="sub text-primary"> {{$product->title}} </span>
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
        <!-- DESCRIPTION_PRODUCT -->
        @include('imonitor::frontend.widgets.description')
        <!-- DESCRIPTION_PRODUCT -->

			<!-- BOTTON_GRAFICAR -->
			<div class="row">
		        <div class="col-12 border-bottom border-top py-2 load-span">
		            <a class="btn btn-primary" href="{{route('imonitor.product.show',$product->id)}}" data-toggle="tooltip" data-placement="top" title="Grafica tiempo Real">
		                <i class="fa fa-area-chart text-white" aria-hidden="true"></i>
		                <span class="d-none d-md-inline-block text-white">Ver el tiempo real</span>
		            </a>
                    @if(Auth::user()->hasAccess('imonitor.alerts.index') && count($product->alersatives) > 0)
                        <a class="btn btn-danger ml-1" href="{{route('imonitor.alerts.product',$product->id)}}" data-toggle="tooltip" data-placement="top" title="Ver las alertas del product">
                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                            <span class="d-none d-md-inline-block text-white">
                                Alertas
                            </span>
                        </a>
                    @endif
		        </div>
				<div class="col-12 my-2">
	            	<div class="d-block text-center load-span">
	            		<input type="text" name="datetimes" id="inputDatatimes" class="form-control datetimes" disabled="true"/>
	            		<div class="d-inline-flex">
	            	    	<button class="btn btn-primary" v-on:click="getDateChart(true)"><span>GRAFICAR</span></button>
	            		</div>
	            	</div>
				</div>
			</div>
			<!-- END-BOTTON_GRAFICAR -->

			<!-- GRAFICA_PRODUCT -->
			<div class="row">
				<div class="col-12">
					@include('imonitor::frontend.widgets.progressLoading')
					<div id="highcharts" v-bind:class="{ 'load-vue': loading }"></div>
				</div>
			</div>
			<!-- END-GRAFICA_PRODUCT -->

			<!-- END-TABLE_PRODUCT -->
				@include('imonitor::frontend.widgets.table')
			<!-- END-TABLE_PRODUCT -->
		</div>
	</div>
	@include('imonitor::frontend.widgets.variables')
@stop

@section('scripts')
    @parent
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script>
	    var startDate = "{{ date('Y/m/d 00:01:00')}}",
	    	endDate = "{{ date('Y/m/d 23:59:00')}}",
	    	alert = "{{ isset($_GET['alert'])? $_GET['alert'] : null }}",
	    	routeExport = "{{ route('imonitor.product.historic.export',$product->id) }}";
	    	// VERIFICA SI EXISTE UNA ALERTA (SE DEBE OPTIMIZAR ESTO)
	    	if(alert  != "")
	    	{
 				startDate = new Date( (new Date(alert)).getTime() - 70000 );
 				startDate = moment(startDate).format('YYYY-MM-DD HH:mm:ss');
 				endDate   = new Date( (new Date(alert)).getTime() + 70000 );
 				endDate   = moment(endDate).format('YYYY-MM-DD HH:mm:ss');
	    	}

        toastr.options = toastr_options;

        const historial = new Vue({
            el: "#content_historic_imonitor",
            components: {DatePick},
            data: {
                loading: true,
                series: {!! json_encode($product->variables) !!},
                product_id: {{ $product->id }},
                client_id: {{ Auth::id() }},
                take: 100,
                page: {
                    total: 0,
                    lastPage: 0,
                    perPage: 0,
                    currentPage: 1
                },
                dataChart: {
                    noData: true,
                    data: [],
                    dataTable: [],
                    title: '{{$product->title}}',
                    chart: null,
                    fromDate: startDate,
                    toDate: endDate,
                    markers: []
                },
            },
            mounted() {
                this.getDateChart();
                this.daterangepicker();
                $('.load-span').addClass('load-hide');
            },
            filters: {
                created_at: function (value) {
                    return moment(new Date(value)).format('DD/MM/YY HH:mm:ss');
                }
            },
            methods: {
                getDateChart: function (reset = false) {
                    if (reset)
                        this.page.currentPage = 1;
                    this.loading = true;
                    this.dataChart.data = [];
                    this.dataChart.dataTable = [];
                    this.series.forEach(element => {
                        this.dataChart.data.push({'name': element.title, 'data': [], 'id': element.id});
                        this.dataChart.dataTable.push({'name': element.title, 'data': [], 'id': element.id});
                    });
                    this.renderChart();
                    Echo.channel('event-' + this.product_id)
                        .listen('.newEvent', (message) => {
                            this.pushEvent(message[0]);
                        });
                    axios.get('{{ url('/api/imonitor/records') }}', {
                        params: {
                            filter: {
                                product: this.product_id,
                                range: [
                                    this.dataChart.fromDate,
                                    this.dataChart.toDate
                                ],
                                // cliente: this.client_id,
                            },
                            take: this.take,
                            page: this.page.currentPage
                        }
                    })
                        .then(response => {
                            this.dataChart.noData = response.data.data.length == 0 ? false : true;

                            response.data.data.forEach(element => {

                                this.dataChart.data.forEach((value, index) => {

                                    if (value.id == element.variable_id) {
                                        time = this.formatDatetime(element.created_at);

                                        value = parseFloat(element.value);

                                        this.dataChart.chart.series[index].addPoint([time, value], true, false);

                                        this.dataChart.dataTable[index].data.push([time, value]);

                                        if (this.series[index].pivot.min_value > value || value > this.series[index].pivot.max_value) {
                                            this.dataChart.chart.series[index].points[0].update(optionAlert);
                                        }
                                    }

                                });
                            });

                            this.page = response.data.meta.page;

                        }).finally(() => {
                        this.loading = false;
                    })
                },
                renderChart: function () {
                    Highcharts.setOptions({
                        global: {
                            useUTC: false
                        }
                    });

                    this.dataChart.chart = Highcharts.chart('highcharts', {
                        chart: {zoomType: 'x'},
                        title: {text: this.dataChart.title},
                        xAxis: {
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
                                formatter: function () {
                                    return moment(new Date(this.value)).format('DD/MM/YY HH:mm:ss');
                                },
                            },
                            shared: true
                        },
                        tooltip: {
                            formatter: function () {
                                return '<b>' + this.series.name + '</b><br>valor: ' + this.y + '<br><small>' + moment(new Date(this.x)).format('DD/MM/YY HH:mm:ss') + '</small>';
                            },
                        },
                        yAxis: {title: {text: 'Valores'}},
                        series: this.dataChart.data,
                        exporting: {
                            showTable: false,
                            buttons: {
                                contextButton: {
                                    // menuItems: ['downloadPNG', 'downloadSVG', 'separator', 'label']
                                }
                            }
                        }

                    });
                },
                change_page: function (id) {
                    this.page.currentPage = id;
                    this.getDateChart();
                },
                pushEvent: function(event) {
                    var badge = $('#badge-push-event');
                    console.log(event);
                    if(badge.hasClass('bg-green'))
                        badge.removeClass('bg-green').addClass('bg-red').find('span').text('APAGADO');
                    else
                        badge.removeClass('bg-red').addClass('bg-green').find('span').text('ENCENDIDO');
                },
                daterangepicker: function () {
                    $('#inputDatatimes').daterangepicker({
                        timePicker: true,
                        startDate: "{{ date('d/m/Y 00:01:00')}}",
                        endDate: "{{ date('d/m/Y 23:59:00')}}",
                        timePicker24Hour: true,
                        maxDate: "{{ date('d/m/Y 23:59:00')}}",
                        opens: 'center',
                        autoclose: false,
                        locale: {
                            format: 'DD/M/Y hh:mm:ss',
                            separator: " - ",
                            applyLabel: "Aplicar",
                            cancelLabel: "Cancelar",
                            fromLabel: "DE",
                            toLabel: "HASTA",
                            customRangeLabel: "Custom",
                            daysOfWeek: [
                                "Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sáb"
                            ],
                            monthNames: [
                                "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
                            ],
                            firstDay: 1
                        },
                        datetimes: 'center'
                    }, (start, end, label) => {
                        this.dataChart.fromDate = start.format('YYYY/MM/DD HH:mm:ss');
                        this.dataChart.toDate = end.format('YYYY/MM/DD HH:mm:ss');
                    });
                    $('#inputDatatimes').attr('disabled', false);
                },
                formatDatetime: function (datetime) {
                    datetime = datetime.split('/');

                    datetime = datetime[1] + '/' + datetime[0] + '/' + datetime[2];

                    return Date.parse(new Date(datetime));
                },
                exportExcel: function () {
                    axios.get(routeExport, {
                        params: {
                            product_id: this.product_id,
                            range: [
                                this.dataChart.fromDate,
                                this.dataChart.toDate
                            ],
                            // cliente: this.client_id,
                        }
                    })
                        .then(response => {
                            toastr.success('Revise su correo para descargar el archivo de exportación.');
                        });
                    toastr.info('Se le enviara a su correo el enlace de descarga.');
                }
            }
        });
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