<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
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
            'is_draft' => ['sometimes', 'required', 'numeric', 'in:0,1'],
            'paginate' => ['sometimes', 'required', 'integer', 'gte:0'],
            'page' => ['sometimes', 'required', 'integer', 'gt:0'],
            'sort' => ['sometimes', 'required', 'string', 'in:id,created_at,title,post_date,category,type_id'],
            'order' => ['required_with:sort', 'string', 'in:asc,desc'],
        ];
    }

    protected function storeRules()
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'numeric'],
            'type_id' => ['required'],
            'post_date' => ['required', 'string', 'date_format:Y-m-d'],
            'is_draft' => ['sometimes', 'required', 'numeric', 'in:0,1'],
        ];
    }

    protected function updateRules()
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['sometimes', 'required', 'string'],
            'category_id' => ['sometimes', 'required', 'numeric'],
            'type_id' => ['sometimes', 'required'],
            'post_date' => ['sometimes', 'required', 'string', 'date_format:Y-m-d'],
            'is_draft' => ['sometimes', 'required', 'numeric', 'in:0,1'],
        ];
    }
}
