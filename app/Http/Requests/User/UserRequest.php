<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = $this->{$this->route()->getActionMethod() . 'Rules'}();

        array_walk($rules, function(&$value, $key) { // add 'bail' to all rules
            array_unshift($value, 'bail');
        });

        return $rules;
    }

    protected function updateRules()
    {
        return [
            'last_name' => ['sometimes', 'required', 'string', 'max:50'],
            'first_name' => ['sometimes', 'required', 'string', 'max:50'],
            'last_name_kana' => ['sometimes', 'required', 'string', 'max:50'],
            'first_name_kana' => ['sometimes', 'required', 'string', 'max:50'],
            'position' => ['sometimes', 'required', 'string', 'max:50'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:250', 'unique:users,email,' . auth()->user()->id],
            'photo' => ['nullable', 'image', 'max:5000'],
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => __('This email address already exists.'),
        ];
    }
}
