<?php

namespace App\Http\Requests\Api;

class AuthorizationRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //alpha_dash 要求数字破折号 ，下划线
        return [
            'username' => 'required|string',
            'password' => 'required|alpha_dash|min:6',
        ];
    }
}
