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
    $router->post('orders/{order}/refund','OrdersController@handleRefund')->name('orders.handle_refund');
    $router->resource('coupon_codes','CouponCodesController');
    $router->resource('categories','CategoryController');
    $router->get('api/categories','CategoryController@apiIndex')->name('api.categories');
});
