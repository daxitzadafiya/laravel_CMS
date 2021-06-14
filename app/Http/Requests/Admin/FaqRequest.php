<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
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

    protected function indexRules()
    {
        return [
            'paginate' => ['sometimes', 'required', 'integer', 'gte:0'],
            'page' => ['sometimes', 'required', 'integer', 'gt:0'],
        ];
    }

    protected function storeRules()
    {
        return [
            'category_id' => [
                'required',
                'numeric',
                'exists:faq_categories,id',
            ],
            'order' => [
                'required',
                'integer',
                'min:0',
            ],
            'question' => [
                'required',
                'string',
                'max:250',
            ],
            'answer' => [
                'required',
                'string',
            ],
            'status' => [
                'required',
                'integer',
                'in:0,1'
            ],
        ];
    }

    protected function updateRules()
    {
        $rules = $this->storeRules();

        array_walk($rules, function(&$value, $key) {
            array_unshift($value, 'sometimes');
        });

        return $rules;
    }
}
