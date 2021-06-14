<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserPreferenceRequest extends FormRequest
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

    protected function updateRules()
    {
        return [
            'preferences' => ['required', 'array'],
            'preferences.*.name' => ['required', 'string', 'in:' . implode(',', data_get(config('reddish.user.preferences'), '*.*.id'))],
            'preferences.*.value' => ['required'],
        ];
    }
}
