<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class FLRatioRequest extends FormRequest
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
            'f_ratio' => ['required', 'numeric', 'min:0'],
            'l_ratio' => ['required', 'numeric', 'min:0'],
        ];
    }
}
