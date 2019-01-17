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
			<div class="row">
				<div class="col-12 title text-dark text-left mb-3">
					<h1 class="sub text-primary"> {{$product->title}} </h1>
	                <div class="line mt-2 bg-secundary"></div>
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <div class="h4 mt-5">
                                {!! $product->description !!}
                            </div>
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
                    <a class="btn btn-primary p-1 ml-1" href="{{ url('monitor/'.$product->id) }}" data-toggle="tooltip" data-placement="top" title="Tiempo Rear">
                        <i class="fa fa-area-chart text-white" aria-hidden="true"></i>
                        <span class="d-none d-md-inline-block text-white">Ver el tiempo real</span>
                    </a>
	            </div>
			</div>
			<!-- END-TITLE -->

			<!-- GRAFICA_PRODUCT -->
			<div class="row">
				<div class="col-12">
					<div class="progress" v-if="loading" style="height: 3px;">
					  	<div class="progress-bar progress-bar-striped progress-bar-animated w-100" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
					<div id="highcharts" v-bind:class="{ 'load-vue': loading }"></div>
				</div>
			</div>
			<!-- END-GRAFICA_PRODUCT -->

			<div class="row">
				<div class="col-12 my-2">
	            	<div class="d-block text-center">
	            		<input type="text" name="datetimes" id="inputDatatimes" class="form-control datetimes load-span" disabled="true" style="opacity: 0"/>
	            	    <button class="btn btn-primary load-span" v-on:click="getDateChart(true)" style="margin-top: -6px;opacity: 0">GRAFICAR</button>
	            	</div>
				</div>
			</div>

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
    <script>
	    var startDate = "{{ date('Y/m/d 00:01:00')}}",
	    	endDate = "{{ date('Y/m/d 23:59:00')}}",
	    	alert = "{{ isset($_GET['alert'])? $_GET['alert'] : null }}";
	    	// VERIFICA SI EXISTE UNA ALERTA
	    	if(alert  != "")
	    	{
 				startDate = new Date( (new Date(alert)).getTime() - 900000 );
 				startDate = moment(startDate).format('YYYY-MM-DD HH:mm:ss');
 				endDate   = new Date( (new Date(alert)).getTime() + 900000 );
 				endDate   = moment(endDate).format('YYYY-MM-DD HH:mm:ss');
	    	}

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
                	title: '{{$product->title}}',
        			chart: null,
                	fromDate: startDate,
                	toDate:  endDate,
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
        		getDateChart: function (reset = false)
        		{
        			if(reset)
        				this.page.currentPage = 1;
                    this.renderChart();
                    this.loading = true;
                    this.dataChart.data = [];
					this.series.forEach(element => {
	        			this.dataChart.data.push({'name': element.title,'data': [], 'id': element.id});
					});
                    this.renderChart();
                    axios.get('{{ url('/api/imonitor/records') }}', {
                        params:{
                            filter:{
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

								if(value.id == element.variable_id)
								{
                        			time = this.formatDatetime(element.created_at);

									value = parseFloat(element.value);

									this.dataChart.chart.series[index].addPoint([time, value], true, false);

		                            if(this.series[index].pivot.min_value > value || value > this.series[index].pivot.max_value) {
		                               var length = this.dataChart.chart.series[index].points.length-1;
		                               console.log(length);
		                               this.dataChart.chart.series[index].points[length].update(optionAlert);
		                               console.log(this.dataChart.chart.series[index].points[length]);
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
            			chart: { zoomType: 'x' },
					    title: { text: this.dataChart.title },
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
					    yAxis: { title: { text: 'Valores' } },
					    series: this.dataChart.data
					});
				},
				change_page: function (id) {
					this.page.currentPage = id;
					this.getDateChart();
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
				                "Dom",
				                "Lun",
				                "Mar",
				                "Mie",
				                "Jue",
				                "Vie",
				                "Sáb"
				            ],
				            monthNames: [
				                "Enero",
				                "Febrero",
				                "Marzo",
				                "Abril",
				                "Mayo",
				                "Junio",
				                "Julio",
				                "Agosto",
				                "Septiembre",
				                "Octubre",
				                "Noviembre",
				                "Diciembre"
				            ],
				            firstDay: 1
				        },
				        datetimes: 'center'
				    },(start, end, label) => {
						this.dataChart.fromDate= start.format('YYYY/MM/DD HH:mm:ss');
						this.dataChart.toDate = end.format('YYYY/MM/DD HH:mm:ss');
				    });
			    	$('#inputDatatimes').attr('disabled',false);
				},
				formatDatetime: function (datetime)
				{
    				datetime = datetime.split('/');

    				datetime = datetime[1]+'/'+datetime[0]+'/'+datetime[2];

					return Date.parse(new Date(datetime));
				}
			}
        });
    </script>

    @if(isset($product->address)&&!empty($product->address))
    	<script type='text/javascript' src="https://maps.googleapis.com/maps/api/js?key={{Setting::get('imonitor::apiMap')}}&extension=.js&output=embed"></script>
	    <script type="text/javascript">
            var geocoder, map, marker,
                latitude  = {{$address->lattitude}},
                longitude = {{$address->longitude}};

	        $(document).ready(function()
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