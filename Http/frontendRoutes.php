<?php
use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/monitor'], function (Router $router) {

    $router->get('/', [
        'as' => 'imonitor.product.index',
        'uses' => 'PublicController@index',
        'middleware' => 'can:imonitor.records.index'
    ]);
    $router->get('email', [
        'as' => 'imonitor.product.email',
        'uses' => 'PublicController@email',
    ]);

    $router->get('/alerts', [
        'as' => 'imonitor.alerts.index',
        'uses' => 'PublicController@alerts',
        'middleware' => 'can:imonitor.alerts.index'
    ]);
    $router->get('/{product}', [
        'as' => 'imonitor.product.show',
        'uses' => 'PublicController@show',
        'middleware' => 'can:imonitor.records.index'
    ]);
    $router->get('/{product}/historic', [
        'as' => 'imonitor.product.historic',
        'uses' => 'PublicController@historic',
        'middleware' => 'can:imonitor.records.index'
    ]);
    $router->get('/{product}/alerts', [
        'as' => 'imonitor.alerts.product',
        'uses' => 'PublicController@alertProduct',
        'middleware' => 'can:imonitor.alerts.index'
    ]);

    $router->post('/alerts/{id}', [
        'as' => 'imonitor.alert.update',
        'uses' => 'PublicController@completeAlert',
        'middleware' => 'can:imonitor.alerts.edit'
    ]);

    $router->get('/{product}/unique', [
        'as' => 'imonitor.product.unique',
        'uses' => 'PublicController@unique',
        'middleware' => 'can:imonitor.product.unique'
    ]);

});