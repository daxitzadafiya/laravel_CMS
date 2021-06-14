<?php

namespace App\Http\Requests\Admin;

use App\Services\CompanyService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SalesGoalRequest extends FormRequest
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
            'term' => [
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (! $this->getBusinessYear()) {
                        $fail(__('Invalid term.'));
                    }
                },
            ],
            'sort' => ['sometimes', 'required', 'string', 'in:id'],
            'order' => ['required_with:sort', 'string', 'in:asc,desc'],
        ];
    }

    protected function showRules()
    {
        return [
            'term' => [
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (! $this->getBusinessYear()) {
                        $fail(__('Invalid term.'));
                    }
                },
            ],
        ];
    }

    protected function storeRules()
    {
       return [
            'term' => [
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (! $this->getBusinessYear()) {
                        $fail(__('Invalid term.'));
                    }
                },
            ],
            'goals' => ['required', 'array'],
            'goals.*.month' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $businessYear = $this->getBusinessYear();

                    if ($businessYear) {
                        $periodMonths = getMonthsInPeriod($businessYear['start_date'], $businessYear['end_date']);

                        if (!in_array($value, $periodMonths)) {
                            $fail(__('Invalid month.'));
                        }
                    }
                },
            ],
            'goals.*.goal' => ['required', 'integer'],
        ];
    }

    public function getBusinessYear()
    {
        $companyService = new CompanyService;

        return $this->input('term')
            ? $companyService->getBusinessYearFromTerm($this->route('company'), $this->input('term'))
            : $this->route('company')->current_business_year;
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
