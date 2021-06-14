<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserGroupRequest extends FormRequest
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
            'paginate' => ['sometimes', 'required', 'integer', 'gte:0'],
            'page' => ['sometimes', 'required', 'integer', 'gt:0'],
            'sort' => ['sometimes', 'required', 'string', 'in:created_at,name'],
            'order' => ['required_with:sort', 'string', 'in:asc,desc'],
        ];
    }

    protected function storeRules()
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:user_groups,name'],
        ];
    }

    protected function updateRules()
    {
        return [
            'name' => [ 'required', 'string', 'max:50', 'unique:user_groups,name,' . $this->route('user_group')->id],
        ];
    }
}
