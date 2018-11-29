@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('imonitor::variables.title.create variable') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.imonitor.variable.index') }}">{{ trans('imonitor::variables.title.variables') }}</a></li>
        <li class="active">{{ trans('imonitor::variables.title.create variable') }}</li>
    </ol>
@stop

@section('content')
    {!! Form::open(['route' => ['admin.imonitor.variable.store'], 'method' => 'post']) !!}
    <div class="row">
        <div class="col-xs-12 col-md-9">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                        </div>
                        <div class="nav-tabs-custom">
                            @include('partials.form-tab-headers')
                            <div class="tab-content">
                                <?php $i = 0; ?>
                                @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                                    <?php $i++; ?>
                                    <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                                        @include('imonitor::admin.variables.partials.create-fields', ['lang' => $locale])
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-header">
                        </div>
                        <div class="box-body ">
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.create') }}</button>
                                <a class="btn btn-danger pull-right btn-flat" href="{{ route('admin.imonitor.product.index')}}">
                                    <i class="fa fa-times"></i> {{ trans('core::core.button.cancel') }}</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {!! Form::close() !!}
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>b</code></dt>
        <dd>{{ trans('core::core.back to index') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.imonitor.variable.index') ?>" }
                ]
            });
        });
    </script>
    <script>
        $( document ).ready(function() {
            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
        });
    </script>
@endpush
