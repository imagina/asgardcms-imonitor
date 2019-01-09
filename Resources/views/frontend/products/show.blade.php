@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ $product->title }} | @parent
@stop
@section('content')

    <div id="content_show_imonitor" class="my-5">
		<div class="container">
			<!-- breadcrumb -->
			<div class="row">
				<nav aria-label="breadcrumb" class="col-12 text-right">
				  	<ol class="breadcrumb rounded-0 pull-right" style="background-color: transparent;">
				    	<li class="breadcrumb-item"><a href="{{ url('/') }}"><span class="text-dark">Inicio</span></a></li>
				    	<li class="breadcrumb-item"><a href="{{ url('/monitor') }}"><span class="text-dark">Monitor</span></a></li>
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
					<div class="h4">
					    {!! $product->description !!}
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
		</div>
	</div>
@stop

@section('scripts')
    @parent
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/series-label.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script>
		var product={{$product->id}};
        const historial = new Vue({
            el: "#content_show_imonitor",
            data: {
                loading: true,
                series: {!! json_encode($product->variables) !!},
        		dataChart: {
                	categories: [],
                	data: [],
        			chart: null,
        			title: '{{$product->title}}',
        			chart: null
        		},
            },
            mounted() {
        		this.getDateChart();
            },
        	methods: {
        		getDateChart: function () {
                    this.renderChart();
                    Echo.channel('record-'+product)
                        .listen('.newRecord', (message) => {
                            this.pushRecord(message[0]);
                        });
        		},
        		renderChart: function () {
					this.series.forEach(element => {
	        			this.dataChart.data.push({'name': element.title,'data': [], 'id': element.id});
					});
					this.dataChart.chart = Highcharts.chart('highcharts', {
					    title: { text: this.dataChart.title },
					    yAxis: { title: { text: 'Valores' } },
					    exporting: { enabled: true },
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
					    series: this.dataChart.data,
					});
				},
				pushRecord: function(record) {
					var shift = this.dataChart.chart.series[0].data.length > 10;
					var created_at = moment(record.created_at);
					this.dataChart.data.forEach((value, index) => {
						if(value.id == record.variable_id){
							this.dataChart.chart.series[index].addPoint([
								+moment(),
								parseFloat(record.value)
							]);
						}
					});
				}
			}
        });
    </script>
@stop