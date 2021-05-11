<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('users','UsersController');
    $router->resource('products','ProductsController');
    $router->resource('orders','OrdersController');
    $router->post('orders/{order}/ship','OrdersController@ship')->name('orders.ship');
});
