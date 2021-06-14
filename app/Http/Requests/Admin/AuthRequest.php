<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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

    private function storeRules()
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
