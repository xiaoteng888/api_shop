<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Api\EmailRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Exceptions\InvalidRequestException;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    //手机注册
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if(!$verifyData){
            abort(403,'验证码已失效');
        }
        //hash_equals防止时序攻击的比较
        if(!hash_equals($verifyData['code'],$request->verification_code)){
            //返回401
            throw new InvalidRequestException('验证码错误',401);
        }
        //创建用户
        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => $request->password,
        ]);
        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return new UserResource($user);
    }

    public function emailStore(EmailRequest $request)
    {
        //创建用户
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);
        return new UserResource($user);
    }
}
