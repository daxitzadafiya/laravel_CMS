<?php

namespace App\Http\Requests\Admin;

use App\Services\CompanyService;
use Illuminate\Foundation\Http\FormRequest;

class GetCompanyPerformanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company' => [
                'bail',
                'required',
                function ($attribute, $value, $fail) {
                    if ($value->status != 1 || ! $value->registration_date) {
                        $fail(__('Company not connected.'));
                    }
                },
            ],
            'term' => [
                'bail',
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $companyService = new CompanyService;

                    if (! $companyService->getBusinessYearFromTerm($this->route('company'), $value)) {
                        $fail(__('Invalid term.'));
                    }
                },
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'company' => $this->route('company'),
        ]);
    }
}
