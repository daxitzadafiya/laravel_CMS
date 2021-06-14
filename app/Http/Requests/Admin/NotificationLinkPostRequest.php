<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NotificationLinkPostRequest extends FormRequest
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
            'page' => ['sometimes', 'required', 'integer','gt:0'],
            'sort' => ['sometimes', 'required', 'string', 'in:id,title,post_date,publisher,clicks,status'],
            'order' => ['required_with:sort', 'string', 'in:asc,desc'],
            'search' => ['nullable', 'string'],
        ];
    }

    protected function storeRules()
    {
        return [
            'post_date' => ['required'],
            'title' => ['required', 'string', 'max:100'],
            'url' => ['required'],
            'publisher' => ['required'],
            'status' => ['required'],
        ];
    }

    protected function updateRules()
    {
        return [
            'post_date' => ['sometimes', 'required'],
            'title' => ['sometimes', 'required', 'string', 'max:100'],
            'url' => ['sometimes', 'required'],
            'publisher' => ['sometimes', 'required'],
            'status' => ['sometimes', 'required'],
        ];
    }
}
