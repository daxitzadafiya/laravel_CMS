<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserRequest extends FormRequest
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
            'sort' => ['sometimes', 'required', 'string', 'in:id,name,email,created_at'],
            'order' => ['required_with:sort', 'string', 'in:asc,desc'],
        ];
    }

    protected function storeRules()
    {
        return [
            'last_name' => ['required', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:250', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'regex:/^.*(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[~`!@#$%^&*()_\-+={\[}\]|\:;"\'<,>\.\?\/]).*$/'],
            'status' => ['required', 'in:0,1'],
        ];
    }

    protected function updateRules()
    {
        return [
            'last_name' => ['sometimes', 'required', 'string', 'max:50'],
            'first_name' => ['sometimes', 'required', 'string', 'max:50'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:250', 'unique:users,email,' . $this->route('admin')->id],
            'status' => ['sometimes', 'required'],
            'photo' => ['nullable', 'image', 'max:5000'],
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => 'Password must contain uppercase, lowercase, number and special character.',
        ];
    }
}
