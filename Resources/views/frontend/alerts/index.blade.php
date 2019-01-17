@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ trans('imonitor::products.title.products') }} | @parent
@stop
@section('content')
<style>
	.pagination li
	{
    	color: #31313F;
	    position: relative;
	    display: block;
	    line-height: 1.25;
	}
	.pagination li:not(:last-child)
	{
	    margin-right: 2px;
	}
	.pagination li *
	{
	    color: #fff;
    	background-color: #FA7F0E;
    	border-color: #FA7F0E;
		display: block;
		padding: 0.5rem 0.75rem;
	}
	.pagination li:hover *,
	.pagination li.active *{
		background-color: #31313f !important;
		border-color: #31313f !important;
	}
	.pagination li.disabled *
	{
	    pointer-events: none;
		background-color: #ff7a0094 !important;
		border-color: #ff7a0094 !important;
	}
</style>
    <div id="content_index_imonitor" class="contaniner-imonitor">
		<div class="container">
            <!-- BREADCRUMB -->
                @component('imonitor::frontend.widgets.breadcrumb')
                    <li class="breadcrumb-item"><a href="{{ url('/monitor') }}"><span class="text-dark">Monitor</span></a></li>
                    @isset ($product)
                    	<li class="breadcrumb-item">
                    		<a href="{{ url("/monitor/$product->id") }}"><span class="text-dark">{{ $product->title }}</span></a>
                    	</li>
                    @endisset
                    <li class="breadcrumb-item active" aria-current="page">Alertas</li>
                @endcomponent
            <!-- END-BREADCRUMB -->

			<!-- TITLE -->
			<div class="row">
				<div class="col-12 title text-dark text-left">
	                <div class="sub text-primary"> Alertas <small>{{ isset($product) ? "($product->title)" : ""}}</small></div>
	                <div class="line mt-2 mb-5 bg-secundary"></div>
	            </div>
			</div>
			<!-- END-TITLE -->

			<!-- ALERTS -->
			<div class="row">
 				@forelse ($alerts  as $index => $alert)
					<div class="col-12">
						<div id="accordionAlert">
						    <div class="row align-items-center border-bottom py-1" id="heading{{$alert->id}}">
						    	<div class="col">
									<div class="mb-0">
										<div class="h4 badge badge-light"> #{{$alert->id}} </div>
										<span class="h4 font-weight-bold text-primary text-uppercase">
											{{$alert->record->variable->title}}
										</span>
										<span class="h6 font-weight-bold">
											| valor: {{$alert->record->value}} 
										</span>
										<samll class="badge badge-light"> {{$alert->created_at->format('d/m/Y h:m:s')}} </samll>
									</div>
						    	</div>
						    	<div class="col-auto">
									status: 
									<span class="label {{$alert->present()->statusLabelClass}}">
                                        {{ $alert->present()->status}}
                                    </span>
                                    <form action="{{ route('imonitor.alert.update',$alert->id) }}" method="POST">
                                    	{{ csrf_field() }}
                                    	<input type="hidden" value="1">
										<button class="btn btn-primary btn-alert_status p-1 ml-1" disabled="true" type="submit">
											<span class="d-none d-md-inline-block">COMPLETADO</span>
										</button>
                                    </form>
						    	</div>
						    </div>
						</div>
					</div>
				@empty
					<div class="col-12">
						<div class="alert alert-dark text-uppercase rounded-0">
							<strong>No existe alertas </strong> para este producto...
						</div>
					</div>
				@endforelse
			
				<div class="col-12 mt-3">
					{{$alerts->links()}}
				</div>
			</div>
			<!-- END-ALERTS -->
		</div>
	</div>
	@include('imonitor::frontend.widgets.variables')
@stop
@section('scripts')
	<script>
		$(function(){
			$('.btn-alert_status').prop('disabled', false);
			changeStatusAlert = function (id_alert)
			{
				if(id_alert) {
                    axios.get('{{ url('/api/imonitor/records') }}', {
                        params:{ }
                    })
                	.then(response => {
                    }).finally(() => { })
				}
			}
		});
	</script>
@stop