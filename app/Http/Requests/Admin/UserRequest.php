<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UserRequest extends FormRequest
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
            'company_id' => ['sometimes', 'required', 'integer'],
            'paginate' => ['sometimes', 'required', 'integer', 'gte:0'],
            'page' => ['sometimes', 'required', 'integer', 'gt:0'],
            'sort' => ['sometimes', 'required', 'string', 'in:id,company,name,position,email,created_at'],
            'order' => ['required_with:sort', 'string', 'in:asc,desc'],
        ];
    }

    protected function storeRules()
    {
        return [
            'company_id' => ['required', 'numeric', 'exists:companies,id'],
            'last_name' => ['required', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name_kana' => ['required', 'string', 'max:50'],
            'first_name_kana' => ['required', 'string', 'max:50'],
            'position' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:250', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'regex:/^.*(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~`!@#$%^&*()_\-+={\[}\]|\:;"\'<,>\.\?\/]).*$/'],
            'photo' => ['nullable', 'image', 'max:5000'],
            'notification_email' => ['nullable', 'boolean'],
            'password_email' => ['nullable', 'boolean'],
            'groups' => ['required', 'array'],
            'groups.*' => ['required', 'integer', 'exists:user_groups,id'],
        ];
    }

    protected function updateRules()
    {
        return [
            'last_name' => ['sometimes', 'required', 'string', 'max:50'],
            'first_name' => ['sometimes', 'required', 'string', 'max:50'],
            'last_name_kana' => ['sometimes', 'required', 'string', 'max:50'],
            'first_name_kana' => ['sometimes', 'required', 'string', 'max:50'],
            'position' => ['sometimes', 'required', 'string', 'max:50'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:250', 'unique:users,email,' . $this->route('user')->id],
            'photo' => ['nullable', 'image', 'max:5000'],
            'groups' => ['sometimes', 'required', 'array'],
            'groups.*' => ['sometimes', 'required', 'integer', 'exists:user_groups,id'],
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => 'Password must contain uppercase, lowercase, number and special character.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->route()->getActionMethod() == 'index') {
            $sortSegments = Str::of($this->sort ?? 'id:asc')->explode(':');

            $this->merge([
                'sort' => $sortSegments[0],
                'order' => $sortSegments[1] ?? 'asc',
            ]);
        }
    }
}
