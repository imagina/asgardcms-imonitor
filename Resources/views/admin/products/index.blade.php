@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('imonitor::products.title.products') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i
                        class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('imonitor::products.title.products') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ route('admin.imonitor.product.create') }}" class="btn btn-primary btn-flat"
                       style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('imonitor::products.button.create product') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="data-table table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('imonitor::products.table.id') }}</th>
                                <th>{{ trans('imonitor::products.table.title') }}</th>
                                <th>{{ trans('imonitor::products.table.user_id') }}</th>
                                <th>{{ trans('imonitor::products.table.variables') }}</th>
                                <th>{{ trans('core::core.table.created at') }}</th>
                                <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (isset($products))
                                @foreach ($products as $product)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.imonitor.product.edit', [$product->id]) }}">
                                                {{ $product->id }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.imonitor.product.edit', [$product->id]) }}">
                                                {{ $product->title }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $product->user->present()->fullname() ??''}}
                                        </td>
                                        <td>
                                            @if(count($product->variables))
                                                @foreach($product->variables as $index=>$variable)
                                                    {{$variable->title}}@if($index!=end($product->variables)),@endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.imonitor.product.edit', [$product->id]) }}">
                                                {{ $product->created_at }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.imonitor.product.edit', [$product->id]) }}"
                                                   class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                                <a href="{{ route('admin.imonitor.record.index', [$product->id]) }}"
                                                   class="btn btn-success btn-flat"><i class="fa fa-bar-chart"></i></a>
                                                <a href="{{ route('admin.imonitor.alert.index', [$product->id]) }}"
                                                   class="btn btn-warning btn-flat"><i class="fa fa-exclamation-triangle"></i></a>
                                                <a href="{{ route('admin.imonitor.event.index', [$product->id]) }}"
                                                   class="btn btn-success btn-flat"><i class="fa fa-snowflake-o"></i></a>
                                                <button class="btn btn-danger btn-flat" data-toggle="modal"
                                                        data-target="#modal-delete-confirmation"
                                                        data-action-target="{{ route('admin.imonitor.product.destroy', [$product->id]) }}">
                                                    <i class="fa fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>{{ trans('imonitor::products.table.id') }}</th>
                                <th>{{ trans('imonitor::products.table.title') }}</th>
                                {{-- <th>{{ trans('imonitor::products.table.user_id') }}</th>--}}
                                <th>{{ trans('core::core.table.created at') }}</th>
                                <th>{{ trans('core::core.table.actions') }}</th>
                            </tr>
                            </tfoot>
                        </table>
                        <!-- /.box-body -->
                    </div>
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
    @include('core::partials.delete-modal')
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>c</code></dt>
        <dd>{{ trans('imonitor::products.title.create product') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $(document).ready(function () {
            $(document).keypressAction({
                actions: [
                    {key: 'c', route: "<?= route('admin.imonitor.product.create') ?>"}
                ]
            });
        });
    </script>
    <?php $locale = locale(); ?>
    <script type="text/javascript">
        $(function () {
            $('.data-table').dataTable({
                "paginate": true,
                "lengthChange": true,
                "filter": true,
                "sort": true,
                "info": true,
                "autoWidth": true,
                "order": [[0, "desc"]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
@endpush
