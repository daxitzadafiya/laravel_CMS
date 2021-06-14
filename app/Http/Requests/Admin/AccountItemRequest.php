<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AccountItemRequest extends FormRequest
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

    protected function indexRules()
    {
        return [
            'category' => ['sometimes', 'required', 'integer'],
            'subtype' => ['nullable', 'string'],
        ];
    }

    protected function updateRules()
    {
        return [
            'account_items' => ['required', 'array'],
            'account_items.*.id' => ['required', 'integer', 'exists:account_items,id,company_id,' . $this->route('company')->id],
            'account_items.*.type' => ['sometimes', 'nullable', 'string', 'in:' . implode(',', array_column(config('reddish.account_item.types'), 'id'))],
            'account_items.*.subtype' => ['sometimes', 'string', 'in:' . implode(',', array_column(config('reddish.account_item.subtypes'), 'id'))],
        ];
    }
}
