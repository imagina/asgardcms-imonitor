@extends('layouts.master')
@section('meta')
@stop
@section('title')
    {{ trans('imonitor::products.title.products') }} | @parent
@stop
@section('content')
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
                @component('imonitor::frontend.widgets.title')
	                <div class="sub text-primary"> Alertas <small>{{ isset($product) ? "($product->title)" : ""}}</small></div>
                @endcomponent
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
										<a class="h4 font-weight-bold text-primary text-uppercase" href="{{ url("monitor/".$alert->product->id."/historic?alert=$alert->created_at") }}">
											{{$alert->record->variable->title}}
										</a>
										<span class="h6">
											@if (!isset($product))
												{{$alert->product->title}}
											@endif
											| valor: {{$alert->record->value}} 
										</span>
										<samll class="badge badge-light"> {{$alert->created_at->format('d/m/Y h:m:s')}} </samll>
									</div>
						    	</div>
						    	<div class="col-auto">
				                    <a class="btn btn-secondary p-1 ml-1 d-inline-block" href="{{ url("monitor/".$alert->product->id."/historic?alert=$alert->created_at") }}" data-toggle="tooltip" data-placement="top" title="Histórico">
				                        <i class="fa fa-history text-white" aria-hidden="true"></i>
				                        <span class="d-none d-md-inline-block text-white">Ir al histórico</span>
				                    </a>
                                    <form action="{{ route('imonitor.alert.update',$alert->id) }}" method="POST" class="d-inline-block">
                                    	{{ csrf_field() }}
                                    	<input type="hidden" value="1">
										<button class="btn btn-primary btn-confirm p-1 ml-1" type="submit" data-toggle="confirmation" data-singleton="true" disabled="false">
											<i class="fa fa-check-square-o" aria-hidden="true"></i>
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
			</div>
			<!-- END-ALERTS -->

			<!-- PAGINATION -->
				<div class="row mt-3">
					{{$alerts->links()}}
				</div>
			<!-- END-PAGINATION -->
		</div>
	</div>

	@include('imonitor::frontend.widgets.variables')
@stop

@section('scripts')
    @parent
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-confirmation2/dist/bootstrap-confirmation.min.js"></script>
	<script>
		$('[data-toggle=confirmation]').confirmation({
		  rootSelector: '[data-toggle=confirmation]',
		  title: '¿Estás seguro?',
		  btnOkLabel: 'SI'
		});
		$('.btn-confirm').removeAttr('disabled');
	</script>
@stop