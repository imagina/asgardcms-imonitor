@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ trans('imonitor::products.title.products') }} | @parent
@stop
@section('content')
    <div id="content_index_imonitor" class="contaniner-imonitor">
		<div class="container">
            <!-- breadcrumb -->
                @component('imonitor::frontend.widgets.breadcrumb')
                    <li class="breadcrumb-item active" aria-current="page">Monitor</li>
                @endcomponent
            <!-- END-breadcrumb -->
			
			<!-- TITLE -->
                @component('imonitor::frontend.widgets.title')
        			<span class="sub text-primary">{{ trans('imonitor::products.title.products') }}</span>
        			<a href="{{ route('imonitor.alerts.index') }}">
        				<span class="badge badge-danger" data-toggle="tooltip" data-placement="top" title="Ver las alertas del producto" style="font-size: 1.5rem">
        					<i class="fa fa-exclamation-circle" aria-hidden="true"></i> {{$alerts}}
        				</span>
        			</a>
                @endcomponent
			<!-- END-TITLE -->

			<!-- PRODUCTS -->
			<div class="row">
				<div class="col-12 mb-3">
					<div class="py-1 mb-2 border-bottom font-weight-bold"></div>
					<div id="map_product" class="border-bottom border-primary bg-light" style="width:100%; height:360px"></div>
				</div>
				<div class="col-12">
					<div class="row">
						<div class="col-12 py-1 mb-2 border-bottom font-weight-bold">
							PRODUCTOS
						</div>
					</div>
					<div class="row">
						@forelse ($products  as $index => $product)
							<div class="col-12">
								<div id="accordionProducts">
								    <div class="row align-items-center py-1 border-bottom" id="heading{{$product->id}}">
								    	<div class="col-auto">
											<div class="text-center badge badge-secondary d-block">
												<span class="d-block">
													{{$product->created_at->format('m')}}/{{$product->created_at->format('m')}}
												</span>
												<p class="mb-0 font-weight-bold"> {{$product->created_at->format('Y')}} </p>
											</div>
											<div class="text-center badge badge-light d-block">
												<sapn class="mb-0 font-weight-bold"> #{{$product->id}} </sapn>
											</div>
								    	</div>
								    	<div class="col btn text-left text-truncate" onclick="centerMap({{$product->address}},{{$index}})">
											<h4>{{$product->title}}</h4>
								    	</div>
								    	<div class="col-auto">
						                    @if(Auth::user()->hasAccess('imonitor.alerts.index') && count($product->alersatives) > 0)
						                        <a class="btn btn-danger ml-1" href="{{route('imonitor.alerts.product',$product->id)}}" data-toggle="tooltip" data-placement="top" title="Ver las alertas del producto">
						                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
						                            <span class="d-none d-md-inline-block text-white">
						                                {{count($product->alersatives)}}
						                            </span>
						                        </a>
						                    @endif

											@if(Auth::user()->hasAccess('imonitor.products.unique'))
	                							<button onclick="window.open('{{ route('imonitor.product.unique',$product->id) }}','newwindow'+{{$product->id}},'width=500,height=500');return false;" class="btn btn-orange-10 ml-1">
	                								<i class="fa fa-window-restore" aria-hidden="true"></i>
	                							    <span class="d-none d-md-inline-block">Abrir ventana</span>
	                							</button>
	                						@endif

											<a class="btn btn-primary ml-1" href="{{ route('imonitor.product.show',$product->id) }}" data-toggle="tooltip" data-placement="top" title="Ir a la grafica de tiempo real">
												<i class="fa fa-area-chart text-white" aria-hidden="true"></i>
												<span class="d-none d-md-inline-block text-white">Tiempo Real</span>
											</a>
											<a class="btn btn-secondary ml-1" href="{{ route('imonitor.product.historic',$product->id) }}" data-toggle="tooltip" data-placement="top" title="Ir a la grafica del Histórico">
												<i class="fa fa-history text-white" aria-hidden="true"></i>
												<span class="d-none d-md-inline-block text-white">Histórico</span>
											</a>
								    	</div>
								    </div>
								</div>
							</div>
						@empty
							<div class="col-12">
								<div class="alert alert-info text-center" role="alert">
									<span>SIN PRODUCTOS</span>
								</div>
							</div>
						@endforelse
					</div>
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
	@include('imonitor::frontend.widgets.variables')
@stop
@section('scripts')
    @parent
    <script type='text/javascript' src="https://maps.googleapis.com/maps/api/js?key={{Setting::get('imonitor::apiMap')}}&extension=.js&output=embed"></script>
    <script>
		var products = {!! json_encode($products) !!},
			id_product = {{ $product->id }}, markers = [],
        	init = false, map, activeInfoWindow;
        function centerMap(location,index)
        {
     		var center = new google.maps.LatLng(parseFloat(location.lattitude), parseFloat(location.longitude));
     		map.panTo(center);
     		map.setZoom(zoom);
     		google.maps.event.trigger(markers[index],'click');
        }

        function initMap(locations, id_product)
        {
			locations.forEach(element => {
				var location = JSON.parse(element.address);
				if(!init){
	        		map = new google.maps.Map(document.getElementById('map_product'), {
	        			zoom: zoom,
	        			center: { lat: parseFloat(location.lattitude), lng: parseFloat(location.longitude) },
                		mapTypeId: google.maps.MapTypeId.ROADMAP,// ROADMAP | SATELLITE | HYBRID | TERRAIN
				        styles: mapStyles
	        		});
	        		init = true;
				}

        		var infowindow = new google.maps.InfoWindow({
        		    content:'<div class="infowindow">' +
			                   '<h6 class="infowindow__location">'+element.title+'</h6>' +
			                   '<p class="infowindow__address"><i class="fa fa-map-marker mr-1"></i>'+location.address+'</p>' +
							   '<a href="monitor/'+element.id+'" class="btn btn-primary btn-sm infowindow__btn">Más detalles</a>' +
			                 '</div>',
			        disableAutoPan: true
        		});

				var marker = new google.maps.Marker({
				    position: { lat: parseFloat(location.lattitude), lng: parseFloat(location.longitude) },
				    map: map,
				    title: location.address
				});

				marker.addListener('click', function() {
				    map.setZoom(zoom);
				    map.setCenter(marker.getPosition());
				});

				markers.push(marker);

		        google.maps.event.addListener(marker, 'click', function() {
		        	if (activeInfoWindow) {
		        		activeInfoWindow.close();
		        	}
		            infowindow.open(map, marker);
		            activeInfoWindow = infowindow;
		        });
			});
        }

        $(function()
        {
			$('[data-toggle="tooltip"]').tooltip();
        	initMap(products.data,id_product);
        });
    </script>
@stop