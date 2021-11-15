<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Api\EmailRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Exceptions\InvalidRequestException;
use App\Http\Resources\UserResource;
use Illuminate\Auth\AuthenticationException;

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

    public function weappStore(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);
        if(!$verifyData){
            abort(403,'验证码已失效');
        }
        // 判断验证码是否相等，不相等反回 401 错误
        if(!hash_equals((string)$verifyData['code'],$request->verification_code)){
            throw new AuthenticationException('验证码错误');
        }
        // 获取微信的 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($request->code);

        if(isset($data['errcode'])){
            throw new AuthenticationException('code不正确');
        }
        // 如果 openid 对应的用户已存在，报错403
        $user = User::where('weapp_openid',$data['openid'])->first();
        if($user){
            throw new AuthenticationException('微信已绑定其它用户，请直接登录');
        }
        // 创建用户
        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => $request->password,
            'weapp_openid' => $data['openid'],
            'weixin_session_key' => $data['session_key'],
        ]);

        return (new UserResource($user))->showSensitiveFields();
    }

    public function show(User $user)
    {
        return (new UserResource($user));
    }

    public function me(Request $request)
    {
        return (new UserResource($request->user()))->showSensitiveFields();
    }
}
