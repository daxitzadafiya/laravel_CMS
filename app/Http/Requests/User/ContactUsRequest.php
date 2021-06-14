<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = $this->{$this->route()->getActionMethod() . 'Rules'}();

        array_walk($rules, function (&$value, $key) { // add 'bail' to all rules
            array_unshift($value, 'bail');
        });

        return $rules;
    }

    protected function storeRules()
    {
        return [
            'type' => ['required', 'string'],
            'message' => ['required', 'string'],
            'referrer_url' => ['required', 'string'],
        ];
    }
}
