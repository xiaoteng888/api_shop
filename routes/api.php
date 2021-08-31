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
                //刷新token
                Route::put('authorizations/current','AuthorizationsController@update')->name('authorizations.update');
                //删除token
                Route::delete('authorizations/current','AuthorizationsController@destroy')->name('authorizations.destroy');
            });
    
            Route::middleware('throttle:'.config('api.rate_limits.access'))->group(function(){
                // 游客可以访问的接口

                // 某个用户的详情
                Route::get('users/{user}','UsersController@show')->name('users.show');
                // 登录后可以访问的接口
                Route::group(['middleware' => ['auth:api']],function(){
                    // 当前登录用户信息
                    Route::get('user','UsersController@me')->name('users.me');
                });
            });
});

