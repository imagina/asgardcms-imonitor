@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ trans('imonitor.products.title') }} | @parent
@stop
@section('content')
	<style>
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
    <div id="content_index_imonitor" class="my-5">
		<div class="container">
			<!-- TITLE -->
			<div class="row">
				<div class="title text-dark text-left">
	                <div class="sub text-primary"> {{ trans('imonitor.products.title') }} </div>
	                <div class="line mt-2 mb-5 bg-secundary"></div>
	            </div>
			</div>
			<!-- END-TITLE -->

			<!-- PRODUCTS -->
			<div class="row">
				<div class="col-12">
					<table class="table table-sm">
						<thead>
							<tr>
							  	<th scope="col" colspan="2" class="border-top-0">
							  		PRODUCTOS
							  		<span title="INFORMACIÓN..."><i class="fa fa-info-circle text-light" aria-hidden="true"></i></span>
							  	</th>
							</tr>
						</thead>
						<tbody>
							@forelse ($products as $product)
    							<tr>
    							  	<th scope="row" class="text-center pt-2" style="width: 50px">
										<div class="text-center badge badge-secondary">
											<small class="d-block">
												{{$product->created_at->format('m')}}/{{$product->created_at->format('m')}}
											</small>
											<p class="mb-0 font-weight-bold"> {{$product->created_at->format('Y')}} </p>
										</div>
										<div class="text-center badge badge-light w-100">
											<p class="mb-0 font-weight-bold"> #{{$product->id}} </p>
										</div>
    							  	</th>
    							  	<td>
										<div class="d-block">
											<a class="h3 mb-0" data-toggle="collapse" href="#collapsProduct{{$product->id}}" role="button" aria-expanded="false" aria-controls="collapsProduct{{$product->id}}">{{$product->title}}
											</a>
				    						<button class="btn btn-primary btn-sm p-1 pull-right" data-toggle="collapse" href="#collapsProduct{{$product->id}}" role="button" aria-expanded="false" aria-controls="collapsProduct{{$product->id}}">
												<i class="fa fa-area-chart text-white" aria-hidden="true"></i>
				    						</button>
										</div>
    							  		{!! empty($product->description) ? null : $product->description !!}
										<div class="collapse" id="collapsProduct{{$product->id}}">
										  	<div class="card card-body pt-0 rounded-0 border-left-0 border-right-0 px-0">
												<ul class="nav nav-pills mb-2 border-bottom" id="pills-tab{{$product->id}}" role="tablist" style="background: #91919180;">
												  	<li class="nav-item">
												    	<a class="nav-link rounded-0 active" id="pills-date-tab{{$product->id}}" data-toggle="pill" href="#pills-date-{{$product->id}}" role="tab" aria-controls="pills-date-{{$product->id}}" aria-selected="false">
												    		<i class="fa fa-calendar-o mr-1" aria-hidden="true"></i> <span class="text-uppercase">Rango</span>
												    	</a>
												  	</li>
													<li class="nav-item">
														<a class="nav-link rounded-0" id="pills-timeline-tab{{$product->id}}" data-toggle="pill" href="#pills-timeline-{{$product->id}}" role="tab" aria-controls="pills-timeline-{{$product->id}}" aria-selected="true" v-on:click="get_timeline({{$product->id}})">
															<i class="fa fa-history mr-1" aria-hidden="true"></i> <span class="text-uppercase">Historial</span>
														</a>
													</li>
												</ul>
												<div class="tab-content">
												  	<div class="tab-pane px-2 show active" id="pills-date-{{$product->id}}" role="tabpanel" aria-labelledby="pills-date-tab{{$product->id}}">
														<div class="row align-items-center">
															<div class="col text-center">
																<p>
																	Lorem ipsum dolor sit amet, consectetur adipisicing elit. Suscipit culpa perferendis vitae quae quo
																</p>
																<date-pick v-model="toDate"></date-pick>
																<date-pick v-model="endDate"></date-pick>
																<div class="d-block text-center">
																	<button class="btn btn-primary btn-sm" v-on:click="get_historial({{$product->id}})">GRAFICAR</button>
																</div>
															</div>
															<div class="col-8">
																<canvas id="chart-{{$product->id}}" class="w-100" style="background: rgba(200, 200, 200, 0.06);"></canvas>
															</div>
														</div>
												  	</div>
													<div class="tab-pane px-2 fade" id="pills-timeline-{{$product->id}}" role="tabpanel" aria-labelledby="pills-timeline-tab{{$product->id}}">
														Lorem ipsum dolor sit amet, consectetur adipisicing elit.
														<canvas id="chart-timeline-{{$product->id}}" class="w-100" style="background: rgba(200, 200, 200, 0.06);"></canvas>
												  	</div>
												</div>
											</div>
										</div>
    							  	</td>
    							</tr>
							@empty
								<tr>
									<th>EMPTY</th>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
			<!-- END-PRODUCTS -->

			<!-- PAGINATION -->
			<div class="row">
				{{$products->links()}}
			</div>
			<!-- END-PAGINATION -->
		</div>
	</div>
@stop
@section('scripts')
	{{--
		npm install vue
		npm install vue-date-pick
		npm install chart.js
		npm install vue-element-loading
	--}}
    @parent
    <script>
	    const vue_index_imonitor = new Vue({
	        el: '#content_index_imonitor',
	        components: {DatePick},
	        data:{
	        	loading: 	true,
	        	toDate: 	'{{date('Y-m-d')}}',
	        	endDate:	'{{date('Y-m-d')}}',
	        	dataChart: {
			        labels: [],
            		backgroundColor: 'rgb(255, 99, 132)',
            		borderColor: 'rgb(255, 99, 132)',
			        datasets: [{
			            label: '',
			            data: []
			        }],
			        borderWidth: 3
	        	},
			    options: {
			        scales: {
			            yAxes: [{
			                ticks: {
			                    beginAtZero:true
			                }
			            }]
			        }
			    }
	        },
	        created: function () { },
	        mounted: function () {
	        	this.loading = false;
	        },
            methods: {
                /*obtiene los productos */
                get_historial: function (product)
                {
	        		this.loading = true;
	        		this.get_datos(product);
                },
                get_timeline: function (product)
                {
	        		this.loading = true;
                },
                get_datos: function (product)
				{
                	axios.get( "{{ url('/api/imonitor/records') }}" + '?filter={"product":'+product+'}&take=100')
               		.then(response => {
		        		this.loading = false;
	                	this.renderChart(product,response.data.data);
					}).finally(() => this.loading = false)

                    Echo.channel('record-' +product)
                    .listen('.newRecord', (message) => {
                    	console.log(message[0]);
                    });
                },
                renderChart: function (product_id,response)
                {
					this.dataChart.datasets[0].label = this.toDate + ' - ' + this.endDate;
					this.dataChart.datasets[0].data = [];
					this.dataChart.labels = [];
					response.forEach(element => {
						this.dataChart.datasets[0].data.push(element.value);
						this.dataChart.labels.push(element.created_at);
					});

					var ctx = document.getElementById('chart-'+product_id);
					new Chart(ctx, {
					    type: 'bar',
					    data: this.dataChart
					});
                }
            }
	    });
	</script>
@stop