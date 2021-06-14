<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => [
                'bail',
                'required',
                'string',
                'email',
                Rule::exists('users')->where(function ($query) {
                    return $query->where('role', 'U');
                }),
            ],
            'reset_url' => [
                'bail',
                'required',
                'string',
                'url',
            ],
        ];
    }
}
