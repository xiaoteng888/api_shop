<?php

namespace App\Http\Requests\Api;

class EmailRequest extends FormRequest
{
    
    public function rules()
    {
        return [
            'email' => 'required|string|max:255|email|unique:users,email',
            'name' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name',
            'password' => 'required|alpha_dash|min:6',
        ];
    }
}
