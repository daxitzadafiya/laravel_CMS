<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CompanyRequest extends FormRequest
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
            'sort' => ['sometimes', 'required', 'string', 'in:id,subscription,type,display_name,status,head_count,business_year,current_month_logins,previous_month_logins,last_login_at'],
            'order' => ['required_with:sort', 'string', 'in:asc,desc'],
            'type' => ['sometimes', 'required', 'string', 'in:' . implode(',', array_column(config('reddish.company.types'), 'id'))],
            'status' => ['sometimes', 'required', 'integer', 'in:' . implode(',', array_column(config('reddish.company.statuses'), 'id'))],
            'search' => ['nullable', 'string'],
        ];
    }

    protected function updateRules()
    {
        $company = $this->route('company');

        $rules = [
            'display_name' => [
                $company->display_name ? 'sometimes' : '',
                'required',
                'string',
                'max:150',
            ],
            'subscription_plan_id' => [
                $company->active_subscription ? 'sometimes' : '',
                'required',
                'numeric',
                'exists:subscription_plans,id',
            ],
            'contact_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'postcode' => [
                'nullable',
                'string',
                'regex:/^[0-9]+$/',
                'size:7',
            ],
            'prefecture_id' => [
                'nullable',
                'numeric',
                'exists:prefectures,id',
            ],
            'city' => [
                'nullable',
                'string',
                'max:250',
            ],
            'address' => [
                'nullable',
                'string',
                'max:250',
            ],
            'phone' => [
                'nullable',
                'numeric',
                'digits_between:1,15',
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:250',
            ],
            'type' => [
                'nullable',
                'string',
                'in:' . implode(',', array_column(config('reddish.company.types'), 'id')),
            ],
            'head_count_id' => [
                'nullable',
                'numeric',
                'exists:head_counts,id',
            ],
            'business_year_start_month' => [
                $company->business_year_start_month ? 'sometimes' : '',
                'required',
                'integer',
                'between:1,12',
            ],
            'business_year_start_day' => [
                $company->business_year_start_day ? 'sometimes' : '',
                'required',
                'integer',
                'between:1,31',
            ],
            'registration_date' => [
                $company->registration_date ? 'sometimes' : '',
                'required',
                'string',
                'date_format:Y-m-d',
                ],
            'status' => [
                $company->status ? 'sometimes' : '',
                'required',
                'numeric',
                'in:' . implode(',', array_column(config('reddish.company.statuses'), 'id')),
            ],
        ];

        if ((int) $this->input('business_year_start_month') == 2) {
            $rules['business_year_start_day'] = ['required', 'integer', 'between:1,29'];
        }

        if (in_array((int) $this->input('business_year_start_month'), [4, 6, 9, 11])) {
            $rules['business_year_start_day'] = ['required', 'integer', 'between:1,30'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'business_year_start_day.between' => 'Invalid day of the month.'
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->route()->getActionMethod() == 'index') {
            $sortSegments = Str::of($this->sort ?? 'id:asc')->explode(':');

            $this->merge([
                'sort' => $sortSegments[0] == 'last_login_date'
                    ? 'last_login_at'
                    : $sortSegments[0],
                'order' => $sortSegments[1] ?? 'asc',
            ]);
        }
    }
}
