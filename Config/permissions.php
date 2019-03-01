<?php

return [
    'imonitor.products' => [
        'index' => 'imonitor::products.list resource',
        'create' => 'imonitor::products.create resource',
        'edit' => 'imonitor::products.edit resource',
        'destroy' => 'imonitor::products.destroy resource',
        'unique'=>'imonitor::products.unique resource'
    ],
    'imonitor.variables' => [
        'index' => 'imonitor::variables.list resource',
        'create' => 'imonitor::variables.create resource',
        'edit' => 'imonitor::variables.edit resource',
        'destroy' => 'imonitor::variables.destroy resource',
    ],
    'imonitor.records' => [
        'index' => 'imonitor::records.list resource',
        'create' => 'imonitor::records.create resource',
        'edit' => 'imonitor::records.edit resource',
        'destroy' => 'imonitor::records.destroy resource',
    ],

    'imonitor.alerts' => [
        'index' => 'imonitor::alerts.list resource',
        'create' => 'imonitor::alerts.create resource',
        'edit' => 'imonitor::alerts.edit resource',
        'destroy' => 'imonitor::alerts.destroy resource',
    ],
    'imonitor.events' => [
        'index' => 'imonitor::events.list resource',
        'create' => 'imonitor::events.create resource',
        'edit' => 'imonitor::events.edit resource',
        'destroy' => 'imonitor::events.destroy resource',
    ],
// append
];
