@isset($product)
    <div class="row">     
        <div class="col mt-3">
            {!!$product->description!!}
        </div>
        @if(isset($product->address) && !empty($product->address))
            <div class="col-md-4 col-sm-12 mt-3">
                <div class="map bg-light">
                    <div class="content">
                        <div id="map_canvas" style="width:100%; height:314px"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endisset

@section('scripts')
    @parent
    @if(isset($product->address) && !empty($product->address))
        @php
            $address= json_decode($product->address)
        @endphp
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
@endsection