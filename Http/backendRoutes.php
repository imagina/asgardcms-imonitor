<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/imonitor'], function (Router $router) {
    $router->bind('product', function ($id) {
        return app('Modules\Imonitor\Repositories\ProductRepository')->find($id);
    });
    $router->get('products', [
        'as' => 'admin.imonitor.product.index',
        'uses' => 'ProductController@index',
        'middleware' => 'can:imonitor.products.index'
    ]);
    $router->get('products/create', [
        'as' => 'admin.imonitor.product.create',
        'uses' => 'ProductController@create',
        'middleware' => 'can:imonitor.products.create'
    ]);
    $router->post('products', [
        'as' => 'admin.imonitor.product.store',
        'uses' => 'ProductController@store',
        'middleware' => 'can:imonitor.products.create'
    ]);
    $router->get('products/{product}/edit', [
        'as' => 'admin.imonitor.product.edit',
        'uses' => 'ProductController@edit',
        'middleware' => 'can:imonitor.products.edit'
    ]);
    $router->put('products/{product}', [
        'as' => 'admin.imonitor.product.update',
        'uses' => 'ProductController@update',
        'middleware' => 'can:imonitor.products.edit'
    ]);
    $router->delete('products/{product}', [
        'as' => 'admin.imonitor.product.destroy',
        'uses' => 'ProductController@destroy',
        'middleware' => 'can:imonitor.products.destroy'
    ]);
    $router->bind('variable', function ($id) {
        return app('Modules\Imonitor\Repositories\VariableRepository')->find($id);
    });
    $router->get('variables', [
        'as' => 'admin.imonitor.variable.index',
        'uses' => 'VariableController@index',
        'middleware' => 'can:imonitor.variables.index'
    ]);
    $router->get('variables/create', [
        'as' => 'admin.imonitor.variable.create',
        'uses' => 'VariableController@create',
        'middleware' => 'can:imonitor.variables.create'
    ]);
    $router->post('variables', [
        'as' => 'admin.imonitor.variable.store',
        'uses' => 'VariableController@store',
        'middleware' => 'can:imonitor.variables.create'
    ]);
    $router->get('variables/{variable}/edit', [
        'as' => 'admin.imonitor.variable.edit',
        'uses' => 'VariableController@edit',
        'middleware' => 'can:imonitor.variables.edit'
    ]);
    $router->put('variables/{variable}', [
        'as' => 'admin.imonitor.variable.update',
        'uses' => 'VariableController@update',
        'middleware' => 'can:imonitor.variables.edit'
    ]);
    $router->delete('variables/{variable}', [
        'as' => 'admin.imonitor.variable.destroy',
        'uses' => 'VariableController@destroy',
        'middleware' => 'can:imonitor.variables.destroy'
    ]);
    $router->bind('record', function ($id) {
        return app('Modules\Imonitor\Repositories\RecordRepository')->find($id);
    });
    $router->get('records', [
        'as' => 'admin.imonitor.record.index',
        'uses' => 'RecordController@index',
        'middleware' => 'can:imonitor.records.index'
    ]);
    $router->get('records/create', [
        'as' => 'admin.imonitor.record.create',
        'uses' => 'RecordController@create',
        'middleware' => 'can:imonitor.records.create'
    ]);
    $router->post('records', [
        'as' => 'admin.imonitor.record.store',
        'uses' => 'RecordController@store',
        'middleware' => 'can:imonitor.records.create'
    ]);
    $router->get('records/{record}/edit', [
        'as' => 'admin.imonitor.record.edit',
        'uses' => 'RecordController@edit',
        'middleware' => 'can:imonitor.records.edit'
    ]);
    $router->put('records/{record}', [
        'as' => 'admin.imonitor.record.update',
        'uses' => 'RecordController@update',
        'middleware' => 'can:imonitor.records.edit'
    ]);
    $router->delete('records/{record}', [
        'as' => 'admin.imonitor.record.destroy',
        'uses' => 'RecordController@destroy',
        'middleware' => 'can:imonitor.records.destroy'
    ]);
// append



});
