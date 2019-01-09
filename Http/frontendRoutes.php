<?php
use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/monitor'], function (Router $router) {

    $router->get('/', [
        'as' => 'imonitor.product.index',
        'uses' => 'PublicController@index',
        'middleware' => 'can:imonitor.records.index'
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
});