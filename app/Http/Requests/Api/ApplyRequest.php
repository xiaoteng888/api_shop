<?php

namespace App\Http\Requests\Api;

class ApplyRequest extends FormRequest
{
    
    public function rules()
    {
        return [
            'reason' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'reason' => '原因',
        ];
    }
}
