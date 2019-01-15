@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ $product->title }} | @parent
@stop
@section('content')
	<style>
		.page-item a {
			background-color: #FA7F0E;
		}
		.page-item:not(.disabled) a:hover {
			background-color: #ef7b11 !important
		}
		.page-item.disabled a {
		    pointer-events: none;
		    background-color: #f3a761 !important
		}
		.load-span{
			position: relative;
		}
		.load-span::after,
		.load-span::before {
			content: '';
			position: absolute;
			z-index: 1000;
			width: 100%;
			height: 100%;
			left: 0;
			top: 0;
			background-color: #8a8a8a3d;
		}
		.load-span::after{
			background-color: white;
			z-index: 999;
		}
		.load-span.hide::after,
		.load-span.hide::before {
			content: none;
		}
		.load-vue{ position: relative; }
		.load-vue:before
		{
			position: absolute;
			content: "";
			z-index: 1000;
			width: 100%;
			height: 100%;
			background-color: white;
			opacity: .8
		}
		#content_index_imonitor .nav-link:not(.active)
		{
			background: rgba(145, 145, 145, 0.5);
		}
		.vdpComponent.vdpWithInput
		{
		    margin-bottom: 10px;
		}
		.vdpComponent.vdpWithInput>button {
			display: none
		}
		.vdpComponent.vdpWithInput>input {
			font-size: 1rem;
		    padding-right: 4px;
		    max-width: 100px;
		    margin-right: 4px;
		    margin-left: 4px;
		    padding-left: 4px;
		    text-align: center;
		    border-radius: 4px;
		}
		#inputDatatimes{
    		display: inline-block;
    		width: auto;
    		min-width: 260px;
    		max-width: 100%;
		}
		.daterangepicker td.active, .daterangepicker td.active:hover
		{
			background-color: #fa7f0e !important;
		}
		.range_inputs .btn-success
		{
			background-color: #fa7f0e !important;
			border-color: #da6f0c !important;
		}
		.btn-success.disabled, .btn-success:disabled
		{
			background-color: #fa7f0e !important;
			border-color: #da6f0c !important;
			opacity: .5
		}
	</style>
    <div id="content_historic_imonitor" class="my-5">
		<div class="container">
			<!-- breadcrumb -->
			<div class="row">
				<nav aria-label="breadcrumb" class="col-12 text-right">
				  	<ol class="breadcrumb rounded-0 pull-right" style="background-color: transparent;">
				    	<li class="breadcrumb-item"><a href="{{ url('/') }}"><span class="text-dark">Inicio</span></a></li>
				    	<li class="breadcrumb-item"><a href="{{ url('/monitor') }}"><span class="text-dark">Monitor</span></a></li>
				    	<li class="breadcrumb-item active" aria-current="page">{{ $product->title }} | Historia </li>
					</ol>
				</nav>
			</div>
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

			<!-- PRODUCT -->
			<div class="row">
				<div class="col-12">
					<div class="progress" v-if="loading" style="height: 3px;">
					  	<div class="progress-bar progress-bar-striped progress-bar-animated w-100" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
					</div>
					<div id="highcharts" v-bind:class="{ 'load-vue': loading }"></div>
				</div>
			</div>

			<div class="row">
				<div class="col-12 my-2">
	            	<div class="d-block text-center">
	            		<input type="text" name="datetimes" id="inputDatatimes" class="form-control datetimes load-span" disabled="true"/>
	            	    <button class="btn btn-primary load-span" v-on:click="getDateChart(true)" style="margin-top: -6px;">GRAFICAR</button>
	            	</div>
				</div>
				<div class="col-12 mb-2 load-span">
					<!-- PAGINADOR -->
					<div class="col-12 text-right my-2 text-center" v-if="page.total > 1">
					    <nav aria-label="Page navigation example">
					        <ul class="pagination justify-content-center mb-0">
					            <!-- btn go to the first page -->
					            <li class="page-item" v-bind:class="{ 'disabled' : page.currentPage == 1 || loading }">
					                <a class="page-link" v-on:click="change_page(1)" title="last page">
					                	<i class="fa fa-angle-double-left" aria-hidden="true"></i>
					                    <span class="sr-only">Last</span>
					                </a>
					            </li>
					            <li class="page-item" v-bind:class="{ 'disabled' : page.currentPage < 2 || loading}">
					                <a class="page-link" v-on:click="change_page(page.currentPage - 1)" title="previo page">
					                    <i class="fa fa-angle-left"></i>
					                    <span class="sr-only">previous</span>
					                </a>
					            </li>
					            <li class="page-item" v-bind:class="{ 'disabled' : page.currentPage == page.lastPage || loading }">
					                <a class="page-link" v-on:click="change_page(page.currentPage + 1 )" title="next page">
					                    <i class="fa fa-angle-right"></i>
					                    <span class="sr-only">next</span>
					                </a>
					            </li>
					            <li class="page-item" v-bind:class="{ 'disabled' : page.currentPage == page.lastPage || loading }">
					                <a class="page-link" v-on:click="change_page(page.lastPage)" title="last page">
					                	<i class="fa fa-angle-double-right" aria-hidden="true"></i>
					                    <span class="sr-only">Last</span>
					                </a>
					            </li>
							</ul>
					    </nav>
					    <div>@{{page.currentPage}} de @{{page.lastPage}} Paginas | <span>Registros: @{{page.total}}</span></div>
					</div>
				</div>
				<div class="col-12 mt-2">
					<div class="table-responsive">
						<table class="table load-span">
							<thead>
								<tr>
									<th v-for="serie in series">
										@{{serie.title}}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-if="dataChart.noData">
									<td v-for="data in dataChart.data">
										<div v-for="val in data.data" class="py-2 border-bottom">
											@{{val[0]}} | <span class="badge badge-secondary">@{{val[1]}}</span>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="alert alert-warning text-center" v-if="!dataChart.noData">
							<strong>NO</strong> HAY DATA PARA ESTE RANGO DE FECHAS...
						</div>
					</div>
				</div>
			</div>
			<!-- END-PRODUCTS -->
		</div>
	</div>

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
	    startDate = "{{ date('d/m/Y 00:01:00')}}";
	    endDate = "{{ date('d/m/Y 23:59:00')}}";
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
        		},
            },
            mounted() {
        		this.getDateChart();
                this.daterangepicker();
                $('.load-span').addClass('hide');
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
                    axios.get('{{ url('/api/imonitor/records') }}', {
                        params:{
                            filter:{
                                product: this.product_id,
                                range: [
                                	this.dataChart.fromDate,
                                	this.dataChart.toDate
                                ],
                                cliente: this.client_id,
                            },
                            take: this.take,
                            page: this.page.currentPage
                        }
                    })
                	.then(response => {
                		this.dataChart.noData = response.data.data.length == 0? false : true;
						response.data.data.forEach(element => {
							this.dataChart.data.forEach((value, index) => {
								if(value.id == element.variable_id){
                        			// time = moment(element.created_at);
                        			// console.log(element.created_at);
                        			// time = new Date(time).getTime();
                        			time = element.created_at;
									this.dataChart.data[index].data.push([
										time,
										parseFloat(element.value)
									]);
								}
							});
						});
							this.page = response.data.meta.page;
                    }).finally(() => { this.loading = false; this.renderChart() })
        		},
        		renderChart: function () {
					this.dataChart.chart = Highcharts.chart('highcharts', {
            			chart: {
            			    zoomType: 'x'
            			},
					    title: { text: this.dataChart.title },
						subtitle: { text: '' },
					    xAxis: {
					        type: 'datetime',
					        dateTimeLabelFormats:
					        {
					        	second: '%H:%M:%S',
					            minute: '%H:%M',
					            hour:'%H:%M',
					            month: '%b \'%y',
					          	day: '%e. %b',
					            year: '%Y',
					        },
						    labels: {
						      format: '{value:  %A, %b %e, %H:%M:%S}'
						    },
					        title: { text: 'Date' }
					    },
                        tooltip: {
                            headerFormat: '<b>{series.name}</b><br>',
                            pointFormat: '{point.x:%B/%e/%Y %H:%M:%S }: valor:{point.y}'
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
						startDate: startDate,
						endDate: endDate,
						timePicker24Hour: true,
						maxDate: "{{ date('d/m/Y 23:59:00')}}",
				        opens: 'center',
				        autoclose: false,
				        locale: {
				        	format: 'DD/M/Y hh:mm',
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
				}
			}
        });
    </script>
    <script type='text/javascript'
        src="https://maps.googleapis.com/maps/api/js?key={{Setting::get('imonitor::apiMap')}}&extension=.js&output=embed"></script>
    @if(isset($product->address)&&!empty($product->address))
    <script type="text/javascript">

        var geocoder;
        var map;
        var marker;

        function initialize() {
            var latitude ={{$address->lattitude}};
            var longitude ={{$address->longitude}};
            var OLD = new google.maps.LatLng(latitude, longitude);
            var options = {
                zoom: 16,
                center: OLD,
                mapTypeId: google.maps.MapTypeId.ROADMAP,// ROADMAP | SATELLITE | HYBRID | TERRAIN
            };
            map = new google.maps.Map(document.getElementById("map_canvas"), options);
            geocoder = new google.maps.Geocoder();
            marker = new google.maps.Marker({
                map: map,
                draggable: false,
                position: OLD
            });
        }

        $(document).ready(function() {
            initialize();

        });
    </script>
    @endif
@stop