<?php

namespace App\Http\Requests\User;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class GoalRequest extends FormRequest
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
        $company = Company::select('id', 'business_year_start_month', 'business_year_start_day', 'registration_date')
            ->findOrFail(auth()->user()->company_id);

        $periodMonths = getMonthsInPeriod($company->current_business_year['start_date'], $company->current_business_year['end_date']);

        return [
            'goals' => ['required', 'array'],
            'goals.*.month' => ['required', 'string', 'in:' . implode(',', $periodMonths)],
            'goals.*.goal' => ['required', 'integer'],
        ];
    }
}
