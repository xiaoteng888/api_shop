<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use App\Models\User;

class AuthorizationsController extends Controller
{
    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;
        filter_var($username,FILTER_VALIDATE_EMAIL) ? $data['email'] = $username : $data['phone'] = $username;
        $data['password'] = $request->password;
        if(!$token = \Auth::guard('api')->attempt($data,$request->has('remember'))){
            throw new InvalidRequestException('帐号或密码不正确',401);
        }

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function respondWithToken($token)
    {
        return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expired_at' => auth('api')->factory()->getTTL() * 60,
               ]);
    }

    //刷新token
    public function update()
    {
        $token = auth('api')->refresh();
        return $this->respondWithToken($token);
    }
    //删除token
    public function destroy()
    {
        auth('api')->logout();
        return response(null,204);
    }

    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;
        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = app('wechat.mini_program');
        $data = $miniProgram->auth->session($code);
        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if(isset($data['errcode'])){
            throw new InvalidRequestException('code不正确',401);
        }
        // 找到 openid 对应的用户
        $user = User::where('weapp_openid',$data['openid'])->first();
        $attributes['weixin_session_key'] = $data['session_key'];
        // 未找到对应用户则需要提交用户名密码进行用户绑定
        if(!$user){
            // 如果未提交用户名密码，403 错误提示
            if(!$request->username){
                throw new InvalidRequestException('用户不存在',403);
            }
            $username = $request->username;
            // 用户名可以是邮箱或电话
            filter_var($username,FILTER_VALIDATE_EMAIL) ? 
                $credentials['email'] = $username :
                $credentials['phone'] = $username;
            $credentials['password'] = $request->password;
            // 验证用户名和密码是否正确
            if(!auth('api')->once($credentials)){
                throw new InvalidRequestException('用户名和密码不正确',403);
            }
            // 获取对应的用户
            $user = auth('api')->getUser();
            $attributes['weapp_openid'] = $data['openid'];
        }
        //更新用户数据
        $user->update($attributes);
        // 为对应用户创建 JWT
        $token = auth('api')->login($user);

        return $this->respondWithToken($token)->setStatusCode(201);
    }
}
