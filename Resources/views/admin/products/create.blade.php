@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('imonitor::products.title.create product') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i
                        class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ route('admin.imonitor.product.index') }}">{{ trans('imonitor::products.title.products') }}</a>
        </li>
        <li class="active">{{ trans('imonitor::products.title.create product') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        {!! Form::open(['route' => ['admin.imonitor.product.store'], 'method' => 'post']) !!}
        <div class="col-xs-12 col-md-9">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary">
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                        </div>
                        <div class="nav-tabs-custom">
                            @include('partials.form-tab-headers')
                            <div class="tab-content">
                                <?php $i = 0; ?>
                                @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                                    <?php $i++; ?>
                                    <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                                        @include('imonitor::admin.products.partials.create-fields', ['lang' => $locale])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6 ">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                        </div>
                        <label>{{trans('imonitor::products.form.user assign')}}</label>
                    </div>
                    <div class="box-body">
                        <select name="user_id" id="user" class="form-control">
                            @foreach ($users as $user)
                                <option value="{{$user->id }}" {{$user->id == $currentUser->id ? 'selected' : ''}}>{{$user->present()->fullname()}}
                                    - ({{$user->email}})
                                </option>
                            @endforeach
                        </select><br>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6 ">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                        class="fa fa-minus"></i>
                            </button>
                        </div>
                        <label>{{trans('imonitor::products.form.operator assign')}}</label>
                    </div>
                    <div class="box-body">
                        <select name="operator_id" id="operator" class="form-control">
                            @foreach ($operators as $operator)
                                <option value="{{$operator->id }}" {{$operator->id == old('operator_id')? 'selected' : ''}}>{{$user->present()->fullname()}}
                                    - ({{$user->email}})
                                </option>
                            @endforeach
                        </select><br>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                    </div>
                    <div class="box-body ">
                        <div class="box-footer">
                            <button type="submit"
                                    class="btn btn-primary btn-flat">{{ trans('core::core.button.create') }}</button>
                            <a class="btn btn-danger pull-right btn-flat"
                               href="{{ route('admin.imonitor.product.index')}}">
                                <i class="fa fa-times"></i> {{ trans('core::core.button.cancel') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-3">
            <div class="row">
                <div class="col-xs-12 ">
                    <div class="box box-primary">
                        <div class="box-header">
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                            <div class="form-group">
                                <label>{{trans('imonitor::variables.form.variables')}}</label>
                            </div>
                        </div>
                        <div class="box-body">
                            @include('imonitor::admin.fields.checklist.variables')
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 ">
                    <div class="box box-primary">
                        <div class="box-header">
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                            <div class="form-group">
                                <label>{{trans('imonitor::products.form.password')}}</label>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class='form-group{{ $errors->has("password") ? ' has-error' : '' }}'>
                                {!! Form::password("password",['class' => 'form-control', 'placeholder' => trans('imonitor::products.form.password')]) !!}
                                {!! $errors->first("password", '<span class="help-block">:message</span>') !!}
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-xs-12 ">
                    <div class="box box-primary">
                        <div class="box-header">
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                            <div class="form-group">
                                <label>{{trans('imonitor::products.form.address')}}</label>
                            </div>
                            <div class="box-body">
                                @include('imonitor::admin.fields.maps',['field'=>['name'=>'address', 'label'=>trans('imonitor::products.form.address')]])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 ">
                    <div class="box box-primary">
                        <div class="box-header">
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                            <div class="form-group">
                                <label>{{trans('imonitor::products.form.maintenance')}}</label>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class='form-group{{ $errors->has("maintenance") ? ' has-error' : '' }}'>
                                <label>
                                    <input type="checkbox" class="flat-blue jsInherit"
                                           name="maintenance"
                                           @if(isset($old["maintenance"])) checked="checked" @endif> {{trans('imonitor::products.form.maintenance')}}
                                </label>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {{-- end nav-tabs-custom --}}
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
        $(document).ready(function () {
            $(document).keypressAction({
                actions: [
                    {key: 'b', route: "<?= route('admin.imonitor.product.index') ?>"}
                ]
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
        });
    </script>
@endpush
