<?php
/**
 * Created by PhpStorm.
 * User: imagina
 * Date: 29/11/2018
 * Time: 12:19 PM
 */

use Illuminate\Routing\Router;

$router->group(['prefix'=>'imonitor'],function (Router $router){

    $router->group(['prefix' => 'products'], function (Router $router) {

        /*Update 2018-10-16. Routes Index and Show for posts*/
        $router->get('/', [
            'as' => 'imonitor.api.products.index',
            'uses' => 'ProductController@index',
        ]);
        $router->get('{param}', [
            'as' => 'imonitor.api.products.show',
            'uses' => 'ProductController@show',
        ]);

        $router->post('/', [
            'as' => 'imonitor.api.products.create',
            'uses' => 'ProductController@create',
            'middleware' =>['auth:api']
        ]);
        $router->post('imonitorproduct', [
            'as' => 'imonitor.api.products.gallery.store',
            'uses' => 'ProductController@galleryStore',
            'middleware' => ['auth:api']
        ]);
        $router->post('imonitorproduct/delete', [
            'as' => 'imonitor.api.products.gallery.delete',
            'uses' => 'ProductController@galleryDelete',
            'middleware' => ['auth:api']
        ]);
        $router->put('{imonitorproduct}', [
            'as' => 'imonitor.api.products.update',
            'uses' => 'ProductController@update',
            'middleware' =>['auth:api']
        ]);
        $router->delete('{imonitorproduct}', [
            'as' => 'imonitor.api.products.delete',
            'uses' => 'ProductController@delete',
            'middleware' => ['auth:api']
        ]);
    });
    $router->group(['prefix' => 'variables'], function (Router $router) {


        $router->get('/', [
            'as' => 'imonitor.api.variables.index',
            'uses' => 'VariableController@index',
        ]);

        $router->get('/', [
            'as' => 'imonitor.api.variables',
            'uses' => 'VariableController@variables',
        ]);
        $router->get('{imonitorerv}', [
            'as' => 'imonitor.api.category',
            'uses' => 'VariableController@category',
        ]);
        $router->get('{imonitorerv}/places', [
            'as' => 'imonitor.api.variables.places',
            'uses' => 'VariableController@places',
        ]);
        $router->post('/', [
            'as' => 'imonitor.api.variables.store',
            'uses' => 'VariableController@store',
            'middleware' => ['api.token','token-can:imonitor.variables.create']
        ]);
        $router->put('{imonitorerv}', [
            'as' => 'imonitor.api.variables.update',
            'uses' => 'VariableController@update',
            'middleware' =>['api.token','token-can:imonitor.variables.edit']
        ]);
        $router->delete('{imonitorerv}', [
            'as' => 'imonitor.api.variables.delete',
            'uses' => 'VariableController@delete',
            'middleware' => ['api.token','token-can:imonitor.variables.destroy']
        ]);
    });
    $router->group(['prefix' => '/records'], function (Router $router) {
        $router->bind('apimonitorrecord', function ($id) {

            return app(\Modules\Imonitor\Repositories\VariableRepository::class)->find($id);
        });

        $router->get('/', [
            'as' => 'imonitor.api.records.index',
            'uses' => 'RecordController@index',
        ]);
        $router->get('historic', [
            'as' => 'imonitor.api.records.historic',
            'uses' => 'RecordController@historic',
        ]);
        $router->get('{apimonitorrecord}', [
            'as' => 'imonitor.api.records',
            'uses' => 'RecordController@store',
        ]);
        $router->post('/save', [
            'as' => 'imonitor.api.record.register',
            'uses' => 'RecordController@register',
            'middleware' => ['auth:api']
        ]);
        $router->post('/', [
            'as' => 'imonitor.api.record.store',
            'uses' => 'RecordController@store',
            'middleware' => ['auth:api']
        ]);
        $router->put('{apimonitorrecord}', [
            'as' => 'imonitor.api.records.update',
            'uses' => 'RecordController@update',
            'middleware' =>['auth:api','token-can:imonitor.variables.edit']
        ]);
        $router->delete('{apimonitorrecord}', [
            'as' => 'imonitor.api.records.delete',
            'uses' => 'RecordController@delete',
            'middleware' => ['auth:api','token-can:imonitor.variables.destroy']
        ]);
    });

    $router->group(['prefix' => '/events'], function (Router $router) {

        $router->get('/', [
            'as' => 'imonitor.api.events.index',
            'uses' => 'EventController@index',
        ]);
        $router->get('{apimonitorevents}', [
            'as' => 'imonitor.api.events.show',
            'uses' => 'EventController@show',
        ]);
        $router->post('/', [
            'as' => 'imonitor.api.record.store',
            'uses' => 'EventController@store',
            'middleware' => ['auth:api']
        ]);
        $router->put('{api.monitor.events}', [
            'as' => 'imonitor.api.events.update',
            'uses' => 'EventController@update',
            'middleware' =>['auth:api','token-can:imonitor.variables.edit']
        ]);
        $router->delete('{api.monitor.events}', [
            'as' => 'imonitor.api.events.delete',
            'uses' => 'EventController@delete',
            'middleware' => ['auth:api','token-can:imonitor.variables.destroy']
        ]);
    });
});