<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\CaptchaRequest;
use  Illuminate\Support\Str;
use Mews\Captcha\Captcha;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request,Captcha $captchaBuilder)
    {
        $key = 'captcha_key' .Str::random(15);
        //  创建验证码 create(生成验证码方式，是否api接口)
        $captcha = $captchaBuilder->create('flat',true);
        /*
        创建的验证码为数组格式：
        'sensitive' => $generator['sensitive'],
        'key' => $generator['key'], 
        最终显示图片
        'img' => data:image/png;base64... 
        key 通过hash算法用来匹配 img 显示的值，可以把 key 和 img 显示的值理解为一个键值对，后面使用 captcha_api_check() 方法来验证用户输入的验证码是否正确*/
        $phone = $request->phone;
        $expiredAt = now()->addMinutes(5);
        \Cache::put($key,['phone' => $phone,'key' => $captcha['key']],$expiredAt);
        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_img' => $captcha['img'],
        ];
        return response()->json($result)->setStatusCode(201);
    }
}
