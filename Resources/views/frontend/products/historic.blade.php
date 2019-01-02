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
	                <div class="sub text-primary"> Pusher Test </div>
	                <div class="line mt-2 bg-secundary"></div>
		            <p class="h4">
		                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Suscipit culpa perferendis vitae quae quo
		            </p>
		            <div class="d-block text-center">
						<date-pick v-model="fromDate"></date-pick>
						<date-pick v-model="toDate"></date-pick>
		                <button class="btn btn-primary btn-sm" v-on:click="getDateChart()">GRAFICAR</button>
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
	{{-- <script src="https://code.highcharts.com/modules/series-label.js"></script> --}}
	{{-- <script src="https://code.highcharts.com/modules/exporting.js"></script> --}}
	{{-- <script src="https://code.highcharts.com/modules/export-data.js"></script> --}}
    <script>
		var product={{$product->id}};
        const historial = new Vue({
            el: "#content_historic_imonitor",
            components: {DatePick},
            data: {
                record: null,
                loading: true,
                product: '{{$product->title}}',
        		dataChart: {
                	categories: [],
                	data: [],
        		},
        		chart: null,
                toDate:  '{{date('Y-m-d')}}',
                fromDate: '{{date('Y-m-d')}}',
                // fromDate: new Date(new Date().setDate(new Date().getDate()-10)),
            },
            mounted() {
        		this.getDateChart();
        		console.log(new Date(new Date().setDate(new Date().getDate()-10)));
        		console.log(new Date("{{date('Y-m-d')}}"));
            },
        	methods: {
        		getDateChart: function ()
        		{
                    this.loading = true;
					this.dataChart.data = [];
					this.dataChart.categories = [];
                    console.log(this.fromDate+'-'+this.toDate);
                    console.log(new Date(new Date(this.fromDate).setHours(0,0,0,0)) +'-'+new Date(new Date(this.toDate).setHours(22,59,0,0)));
                    axios.get('https://mtr-monitor.imaginacolombia.com/api/imonitor/records', {
                        params:{
                            filter:{
                                product:product,
                                range:[new Date(new Date(this.fromDate).setHours(0,0,0,0)),new Date(new Date(this.toDate).setHours(22,59,0,0))]
                            },
                            take:100,
                        }
                    })
                	.then(response => {
                        this.record = response.data.data;
						this.record.forEach(element => {
							this.dataChart.data.unshift(parseFloat(element.value));
							this.dataChart.categories.unshift(element.created_at);
						});
                    }).finally(() => {
                    	this.loading = false;
                    	this.renderChart();
                    })
        		},
        		renderChart: function ()
        		{
					this.chart = Highcharts.chart('highcharts', {
					    title: {
					        text: this.product
					    },
					    yAxis: { title: { text: 'Valores' } },
					    subtitle: { text: '' },
						xAxis: {
    						type: 'datetime',
    						dateTimeLabelFormats: {
    							day: '%d %b %Y'
    						},
						    categories: this.dataChart.categories
						},
					    legend: {
					        layout: 'vertical',
					        align: 'right',
					        verticalAlign: 'middle'
					    },

					    series: [{
					        data: this.dataChart.data
					    }],
					    exporting: {
					        enabled: false
					    },
					    responsive: {
					        rules: [{
					            condition: {
					                maxWidth: 500
					            },
					            chartOptions: {
					                legend: {
					                    layout: 'horizontal',
					                    align: 'center',
					                    verticalAlign: 'bottom'
					                }
					            }
					        }]
					    }
					});
				}
			}
        });
    </script>
@stop