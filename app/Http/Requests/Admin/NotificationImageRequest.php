<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NotificationImageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'image' => ['required', 'image', 'max:5000'],
        ];
    }

    public function messages()
    {
        return [
            'image.image' => 'Not an image file.',
            'image.max' => 'The image must not be greater than 5mb.',
        ];
    }
}
