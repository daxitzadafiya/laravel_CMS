<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'current_password' => [
                'bail',
                'required',
            ],
            'new_password' => [
                'bail',
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^.*(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~`!@#$%^&*()_\-+={\[}\]|\:;"\'<,>\.\?\/]).*$/',
                'confirmed'
            ]
        ];
    }

    public function messages()
    {
        return [
            'new_password.regex' => 'Password must contain uppercase, lowercase, number and special character.',
            'new_password.confirmed' => 'Password and Confirm Password must be same.',
        ];
    }
}
