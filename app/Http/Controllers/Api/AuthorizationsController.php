<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Exceptions\InvalidRequestException;

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
                'expired_at' => auth('api')->factory()->getTTL() * 60
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
}
