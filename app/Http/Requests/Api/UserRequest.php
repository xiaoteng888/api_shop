<?php

namespace App\Http\Requests\Api;

class UserRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name',
            'password' => 'required|alpha_dash|min:6',
            'verification_key' => 'required|string',
            'verification_code' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
            'verification_key' => '验证码Key',
            'verification_code' => '验证码',
        ];
    }
}
