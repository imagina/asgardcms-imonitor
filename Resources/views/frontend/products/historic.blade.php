@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ $product->title }} | @parent
@stop
@section('content')
	<style>
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
				<div class="col-12 title text-dark text-left mb-5">
	                <div class="sub text-primary"> {{$product->title}} </div>
	                <div class="line mt-2 bg-secundary"></div>
					<div class="h4">
						{!! $product->description !!}
					</div>
		            <div class="d-block text-center">
		            	<input type="text" name="datetimes" id="inputDatatimes" class="form-control datetimes" disabled="true"/>
		                <button class="btn btn-primary mt-2" v-on:click="getDateChart()">GRAFICAR</button>
		            </div>
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
			<!-- END-PRODUCTS -->

			<div class="row">
				<div class="col-12">
					<table class="table table-striped">
						<thead>
						<tr>
							<th scope="col">{{}}</th>
							<th scope="col">First</th>
							<th scope="col">Last</th>
							<th scope="col">Handle</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<th scope="row">1</th>
							<td>Mark</td>
							<td>Otto</td>
							<td>@mdo</td>
						</tr>
						<tr>
							<th scope="row">2</th>
							<td>Jacob</td>
							<td>Thornton</td>
							<td>@fat</td>
						</tr>
						<tr>
							<th scope="row">3</th>
							<td>Larry</td>
							<td>the Bird</td>
							<td>@twitter</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
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
                client_id: {{ $currentUser->id }},
        		dataChart: {
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
            },
        	methods: {
        		getDateChart: function ()
        		{
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
                                //cliente: this.client_id
                            },
                            take:100,
                        }
                    })
                	.then(response => {
                    	console.log(response.data.data);
						response.data.data.forEach(element => {
							this.dataChart.data.forEach((value, index) => {
								if(value.id == element.variable_id){
									this.dataChart.chart.series[index].addPoint([
										+moment(),
										parseFloat(element.value)
									]);
								}
							});
						});
                    }).finally(() => {
                    	console.log(this.dataChart.data);
                    	this.loading = false;
                    })
        		},
        		renderChart: function (){
					this.dataChart.chart = Highcharts.chart('highcharts', {
					    title: { text: this.dataChart.title },
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
						      format: '{value: %e/%m/%y %H:%M:%S}'
						    },
					    },
					    yAxis: { title: { text: 'Valores' } },
					    exporting: { enabled: true },
					    series: this.dataChart.data,
					});
				},
				daterangepicker: function ()
				{
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
				    	// console.log(this.dataChart.fromDate);
				    	// console.log(this.dataChart.toDate);
						this.dataChart.fromDate= start.format('YYYY/MM/DD HH:mm:ss');
						this.dataChart.toDate = end.format('YYYY/MM/DD HH:mm:ss');
				    });
			    	$('#inputDatatimes').attr('disabled',false);
				}
			}
        });
    </script>
@stop