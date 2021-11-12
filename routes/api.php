<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')
           ->namespace('Api')
           ->name('api.v1.')
           ->group(function(){

            Route::middleware('throttle:'.config('api.rate_limits.sign'))->group(function(){
                //图片验证码
                Route::post('captchas','CaptchasController@store')->name('captchas.store');
                //短信验证码
                Route::post('verificationCodes','VerificationCodesController@store')->name('verificationCodes.store');
                //用户手机注册路由
                Route::post('users','UsersController@store')->name('users.store');
                //用户邮箱注册路由
                Route::post('userEmails','UsersController@emailStore')->name('userEmails.store');
                //登录
                Route::post('authorizations','AuthorizationsController@store')->name('authorizations.store');
                //小程序登录
                Route::post('weapp/authorizations','AuthorizationsController@weappStore')->name('weapp.authorizations.store');
                //小程序注册
                Route::post('weapp/users','UsersController@weappStore')->name('weapp.users.store');
                //刷新token
                Route::put('authorizations/current','AuthorizationsController@update')->name('authorizations.update');
                //删除token
                Route::delete('authorizations/current','AuthorizationsController@destroy')->name('authorizations.destroy');
            });
    
            Route::middleware('throttle:'.config('api.rate_limits.access'))->group(function(){
                // 游客可以访问的接口
                
                // 某个用户的详情
                Route::get('users/{user}','UsersController@show')->name('users.show');
                //商品列表
                Route::get('products','ProductsController@index')->name('products.index');
                
                //所有类目
                Route::get('categories','ProductsController@categories')->name('categories.index');
                // 登录后可以访问的接口
                Route::group(['middleware' => ['auth:api']],function(){
                    // 当前登录用户信息
                    Route::get('user','UsersController@me')->name('users.me');
                    //收藏列表
                    Route::get('products/favorites','ProductsController@favorites')->name('products.favorites');
                    //收藏商品
                    Route::post('products/{product}/favorite','ProductsController@favor')->name('products.favor');
                    //取消收藏商品
                    Route::delete('products/{product}/favorite','ProductsController@disfavor')->name('products.disfavor');
                    //用户加入购物车操作
                    Route::post('cart','CartController@add')->name('cart.add');
                    //购物车列表
                    Route::get('cart','CartController@index')->name('cart.index');
                    //移除购物车
                    Route::delete('cart/{sku}','CartController@remove')->name('cart.remove');
                    //优惠券
                    Route::get('coupon_codes/{code}','CouponCodeController@show')->name('coupon_codes.show');
                    //用户地址列表
                    Route::get('user_addresses','UserAddressesController@index')->name('user_addresses.index');
                    //新增收货地址
                    Route::post('user_addresses','UserAddressesController@store')->name('user_addresses.store');
                    //修改收货地址
                    Route::patch('user_addresses/{user_address}','UserAddressesController@update')->name('user_addresses.update');
                    //删除收货地址
                    Route::delete('user_addresses/{user_address}','UserAddressesController@destroy')->name('user_addresses.destroy');
                    //订单列表
                    Route::get('orders','OrdersController@index')->name('orders.index');
                    //订单详情
                    Route::get('orders/{order}','OrdersController@show')->name('orders.show');
                    //提交订单
                    Route::post('orders','OrdersController@store')->name('orders.store');
                    //众筹订单提交
                    Route::post('crowdfunding_orders','OrdersController@crowdfunding')->name('crowdfunding_orders.store');
                    //用户确认收货
                    Route::post('orders/{order}/received','OrdersController@received')->name('orders.received');
                    //商品评价页
                    Route::get('orders/{order}/review','OrdersController@review')->name('orders.review');
                    //提交商品评价
                    Route::post('orders/{order}/review','OrdersController@sendReview')->name('orders.review.store');
                    //申请退款
                    Route::post('orders/{order}/apply_refund','OrdersController@applyRefund')->name('orders.apply_refund');                    //订单支付宝支付
                    Route::get('payment/{order}/alipay','PaymentController@payByAlipay')->name('payment.alipay');
                    //支付宝前端回调
                    Route::get('payment/alipay/return','PaymentController@alipayReturn')->name('payment.alipay.return');
                    //分期付款列表
                    Route::get('installments','InstallmentsController@index')->name('installments.index');
                    //创建分期付款
                    Route::post('payment/{order}/installment','PaymentController@payByInstallment');
                    //分期付款详情
                    Route::get('installments/{installment_id}','InstallmentsController@show')->name('installments.show');
                    //支付宝分期付款
                    Route::get('installments/{installment}/alipay','InstallmentsController@payByAlipay')->name('installments.alipay');
                    //支付宝分期付款前端回调
                    Route::get('installments/alipay/return','InstallmentsController@alipayReturn')->name('installments.alipay.return');


                });
                //产品详情
                Route::get('products/{product_id}','ProductsController@show')->name('products.show');
                //支付宝服务器端回调
                Route::post('payment/alipay/notify','PaymentController@alipayNotify')->name('payment.alipay.notify');
                //支付宝分期付款服务端回调
                Route::post('installments/alipay/notify','InstallmentsController@alipayNotify')->name('installments.alipay.notify');
            });
});

