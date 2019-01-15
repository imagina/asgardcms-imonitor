@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ trans('imonitor::products.title.products') }} | @parent
@stop
@section('content')
	<style>
		.infowindow {
		    position: relative;
		    padding: 15px;
		    background: #fff;
		    min-width: 200px;
		    max-width: 100%;
		}
		.infowindow__address
		{
			color: #777777
		}
		.infowindow__btn
		{
			color: white;
			border-radius: 0px;
			text-transform: uppercase;
		}
	</style>
    <div id="content_index_imonitor" class="my-5">
		<div class="container">
			<!-- TITLE -->
			<div class="row">
				<div class="title text-dark text-left">
	                <div class="sub text-primary"> {{ trans('imonitor::products.title.products') }} </div>
	                <div class="line mt-2 mb-5 bg-secundary"></div>
	            </div>
			</div>
			<!-- END-TITLE -->

			<!-- PRODUCTS -->
			<div class="row">
				<div class="col-12 mb-3">
					<div class="py-1 mb-2 border-bottom font-weight-bold">
					</div>
					<div id="map_product" class="border-bottom border-primary bg-light" style="width:100%; height:360px"></div>
				</div>
				<div class="col-12">
					<div class="row">
						<div class="col-12 py-1 mb-2 border-bottom font-weight-bold">
							PRODUCTOS
							<span title="INFORMACIÓN..."><i class="fa fa-info-circle text-light" aria-hidden="true"></i></span>
						</div>
					</div>
					<div class="row">
						@forelse ($products as $product)
							<div class="col-12">
								<div id="accordionProducts">
								    <div class="row py-1 border-bottom" id="heading{{$product->id}}">
								    	<div class="col px-0" style="max-width: 60px">
											<div class="text-center badge badge-secondary d-block">
												<small class="d-block">
													{{$product->created_at->format('m')}}/{{$product->created_at->format('m')}}
												</small>
												<p class="mb-0 font-weight-bold"> {{$product->created_at->format('Y')}} </p>
											</div>
											<div class="text-center badge badge-light d-block">
												<sapn class="mb-0 font-weight-bold"> #{{$product->id}} </sapn>
											</div>
								    	</div>
								    	<div class="col btn text-left text-truncate" onclick="centerMap({{$product->address}})">
											{{$product->title}}
								    	</div>
								    	<div class="col px-0 align-self-center" style="max-width: 195px">
											<a class="btn btn-primary p-1 ml-1" href="{{ url('monitor/'.$product->id) }}" data-toggle="tooltip" data-placement="top" title="Tiempo Rear">
												<i class="fa fa-area-chart text-white" aria-hidden="true"></i>
												<span class="d-none d-md-inline-block text-white">Tiempo Real</span>
											</a>
											<a class="btn btn-secondary p-1 ml-1" href="{{ url('monitor/'.$product->id.'/historic') }}" data-toggle="tooltip" data-placement="top" title="Histórico">
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
@stop
@section('scripts')
    @parent
    <script type='text/javascript' src="https://maps.googleapis.com/maps/api/js?key={{Setting::get('imonitor::apiMap')}}&extension=.js&output=embed"></script>
    <script>
    	var zoom = 16;
		$(function () {
			$('[data-toggle="tooltip"]').tooltip()
		})
		var products = {!! json_encode($products) !!},
			id_product = {{ $product->id }},
        	init = false, map,
        	iconMarker = "http://wakefulnessmagicpill.com/wp-content/uploads/2018/11/Map-Marker-Xxl-Great-Map-Marker.jpg";

        centerMap = function(location) {
     		var center = new google.maps.LatLng(parseFloat(location.lattitude), parseFloat(location.longitude));
     		map.panTo(center);
     		map.setZoom(zoom);
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
				        styles: [
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
						]
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
				    // icon: iconMarker
				});

				marker.addListener('click', function() {
				    map.setZoom(zoom);
				    map.setCenter(marker.getPosition());
				});

		        google.maps.event.addListener(marker, 'click', function() {
		            infowindow.open(map, marker);
		        });
			});
        }

        $(function()
        {
        	initMap(products.data,id_product);
        });

    </script>
@stop