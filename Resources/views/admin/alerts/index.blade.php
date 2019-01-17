@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('imonitor::alerts.title.alerts') }} - {{$product->title}} - {{$product->user->present()->fullname() ??''}}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i
                        class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('imonitor::alerts.title.alerts') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">

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
                                <th>Id</th>
                                <th>{{ trans('imonitor::variables.title.variables') }}</th>
                                <th>{{ trans('imonitor::records.table.value') }}</th>
                                <th>{{ trans('imonitor::alerts.table.status') }}</th>
                                <th>{{ trans('core::core.table.created at') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($alerts)): ?>
                            <?php foreach ($alerts as $alert): ?>
                            <tr>
                                <td>
                                    {{ $alert->id }}
                                </td>
                                <td>
                                    {{ $alert->record->variable->title??'' }}
                                </td>
                                <td>
                                    {{ $alert->record->value }}
                                </td>
                                <td>
                                     <span class="label {{$alert->present()->statusLabelClass}}">
                                            {{ $alert->present()->status}}
                                    </span>
                                </td>
                                <td>
                                    {{ $alert->created_at }}
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Id</th>
                                <th>{{ trans('imonitor::variables.title.variables') }}</th>
                                <th>{{ trans('imonitor::records.table.value') }}</th>
                                <th>{{ trans('imonitor::alerts.table.status') }}</th>
                                <th>{{ trans('core::core.table.created at') }}</th>
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
        <dd>{{ trans('imonitor::alerts.title.create alert') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script type="text/javascript">
        $(document).ready(function () {
            $(document).keypressAction({
                actions: [
                    {key: 'c', route: "<?= route('admin.imonitor.alert.create') ?>"}
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
