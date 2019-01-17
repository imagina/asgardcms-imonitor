<?php

use Illuminate\Routing\Router;

/** @var Router $router */

$router->group(['prefix' => '/imonitor'], function (Router $router) {
    $router->group(['prefix' => '/products'], function (Router $router) {
        $router->bind('imonitoradproduct', function ($id) {
            return app('Modules\Imonitor\Repositories\ProductRepository')->find($id);
        });
        $router->get('/', [
            'as' => 'admin.imonitor.product.index',
            'uses' => 'ProductController@index',
            'middleware' => 'can:imonitor.products.index'
        ]);
        $router->get('create', [
            'as' => 'admin.imonitor.product.create',
            'uses' => 'ProductController@create',
            'middleware' => 'can:imonitor.products.create'
        ]);
        $router->post('/', [
            'as' => 'admin.imonitor.product.store',
            'uses' => 'ProductController@store',
            'middleware' => 'can:imonitor.products.create'
        ]);
        $router->get('{imonitoradproduct}/edit', [
            'as' => 'admin.imonitor.product.edit',
            'uses' => 'ProductController@edit',
            'middleware' => 'can:imonitor.products.edit'
        ]);
        $router->put('{imonitoradproduct}', [
            'as' => 'admin.imonitor.product.update',
            'uses' => 'ProductController@update',
            'middleware' => 'can:imonitor.products.edit'
        ]);
        $router->delete('{imonitoradproduct}', [
            'as' => 'admin.imonitor.product.destroy',
            'uses' => 'ProductController@destroy',
            'middleware' => 'can:imonitor.products.destroy'
        ]);
    });
    $router->group(['prefix' => '/variables'], function (Router $router) {
        $router->bind('imonitoradvariable', function ($id) {
            return app('Modules\Imonitor\Repositories\VariableRepository')->find($id);
        });
        $router->get('/', [
            'as' => 'admin.imonitor.variable.index',
            'uses' => 'VariableController@index',
            'middleware' => 'can:imonitor.variables.index'
        ]);
        $router->get('create', [
            'as' => 'admin.imonitor.variable.create',
            'uses' => 'VariableController@create',
            'middleware' => 'can:imonitor.variables.create'
        ]);
        $router->post('/', [
            'as' => 'admin.imonitor.variable.store',
            'uses' => 'VariableController@store',
            'middleware' => 'can:imonitor.variables.create'
        ]);
        $router->get('{imonitoradvariable}/edit', [
            'as' => 'admin.imonitor.variable.edit',
            'uses' => 'VariableController@edit',
            'middleware' => 'can:imonitor.variables.edit'
        ]);
        $router->put('{imonitoradvariable}', [
            'as' => 'admin.imonitor.variable.update',
            'uses' => 'VariableController@update',
            'middleware' => 'can:imonitor.variables.edit'
        ]);
        $router->delete('{imonitoradvariable}', [
            'as' => 'admin.imonitor.variable.destroy',
            'uses' => 'VariableController@destroy',
            'middleware' => 'can:imonitor.variables.destroy'
        ]);

    });


    $router->group(['prefix' => '/records'], function (Router $router) {
        $router->bind('record', function ($id) {
            return app('Modules\Imonitor\Repositories\RecordRepository')->find($id);
        });
        $router->get('{product}/index', [
            'as' => 'admin.imonitor.record.index',
            'uses' => 'RecordController@index',
            'middleware' => 'can:imonitor.records.index'
        ]);
        $router->get('/create', [
            'as' => 'admin.imonitor.record.create',
            'uses' => 'RecordController@create',
            'middleware' => 'can:imonitor.records.create'
        ]);
        $router->post('/', [
            'as' => 'admin.imonitor.record.store',
            'uses' => 'RecordController@store',
            'middleware' => 'can:imonitor.records.create'
        ]);
        $router->get('/{record}/edit', [
            'as' => 'admin.imonitor.record.edit',
            'uses' => 'RecordController@edit',
            'middleware' => 'can:imonitor.records.edit'
        ]);
        $router->put('/{record}', [
            'as' => 'admin.imonitor.record.update',
            'uses' => 'RecordController@update',
            'middleware' => 'can:imonitor.records.edit'
        ]);
        $router->delete('/{record}', [
            'as' => 'admin.imonitor.record.destroy',
            'uses' => 'RecordController@destroy',
            'middleware' => 'can:imonitor.records.destroy'
        ]);
// append
    });
    $router->group(['prefix' => 'alerts'], function (Router $router) {
        $router->bind('alert', function ($id) {
            return app('Modules\Imonitor\Repositories\AlertRepository')->find($id);
        });
        $router->get('{product}/index', [
            'as' => 'admin.imonitor.alert.index',
            'uses' => 'AlertController@index',
            'middleware' => 'can:imonitor.alerts.index'
        ]);
        $router->get('/create', [
            'as' => 'admin.imonitor.alert.create',
            'uses' => 'AlertController@create',
            'middleware' => 'can:imonitor.alerts.create'
        ]);
        $router->post('/', [
            'as' => 'admin.imonitor.alert.store',
            'uses' => 'AlertController@store',
            'middleware' => 'can:imonitor.alerts.create'
        ]);
        $router->get('/{alert}/edit', [
            'as' => 'admin.imonitor.alert.edit',
            'uses' => 'AlertController@edit',
            'middleware' => 'can:imonitor.alerts.edit'
        ]);
        $router->put('/{alert}', [
            'as' => 'admin.imonitor.alert.update',
            'uses' => 'AlertController@update',
            'middleware' => 'can:imonitor.alerts.edit'
        ]);
        $router->delete('/{alert}', [
            'as' => 'admin.imonitor.alert.destroy',
            'uses' => 'AlertController@destroy',
            'middleware' => 'can:imonitor.alerts.destroy'
        ]);
    });

});
