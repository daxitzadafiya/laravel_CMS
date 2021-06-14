<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends FormRequest
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
                    return $query->whereIn('role', ['A', 'SA']);
                }),
            ],
            'token' => [
                'bail',
                'required',
                'string',
            ],
            'password' => [
                'bail',
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^.*(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~`!@#$%^&*()_\-+={\[}\]|\:;"\'<,>\.\?\/]).*$/',
                'confirmed',
            ],
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => 'Password must contain uppercase, lowercase, number and special character.',
        ];
    }
}
