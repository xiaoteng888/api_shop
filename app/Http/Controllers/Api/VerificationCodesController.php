<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Support\Str;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request)
    {
        $phone = $request->phone;
        if(!app()->environment('production')){
            $code = '1234';
        }else{
            //随机生成四位数
            $code = str_pad(random_int(1,9999), 4,0,STR_PAD_LEFT);
            try{
                $result = $easySms->send($phone,[
                    'template' => config('easysms.gateways.aliyun.templates.register'),
                    'data' => [
                        'code' => $code,
                    ],
                ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception){
                $message = $exception->getException('aliyun')->getMessage();
                abort(500, $message ?: '短信发送异常');
            }
        }
        //获取一个15位随机字符串
        $key = 'verificationCode_'.Str::random(15);
        //验证码过期时间
        $expiredAt = now()->addMinutes(5);
        // 缓存验证码 5 分钟过期。
        \Cache::put($key,['phone' => $phone,'code' => $code],$expiredAt);
        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt,
        ])->setStatusCode(201);
    }
}
