<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/**
 * Shipping routes
 */
$router->get('/shipping', 'ShippingController@index');
$router->post('/shipping', 'ShippingController@store');
$router->get('/shipping/{shipping}', 'ShippingController@show');
$router->get('/shipping/order/{order_id}', 'ShippingController@showByOrder');
$router->put('/shipping/{shipping}', 'ShippingController@update');
$router->patch('/shipping/{shipping}', 'ShippingController@update');
$router->post('/shipping/calculate', 'ShippingController@calculate');
