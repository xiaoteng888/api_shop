<?php

namespace App\Http\Requests\Api;

class VerificationCodeRequest extends FormRequest
{
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'captcha_key' => 'required|string',
            'captcha_code' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'captcha_key' => '图片验证码KEY',
            'captcha_code' => '图片验证码',
        ];
    }
}
