<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ContactUsRequest;
use App\Mail\EmailContactUs;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function store(ContactUsRequest $request)
    {
        $adminUser = User::select('email')->where('role', 'SA')->first();

        Mail::to($adminUser->email)
            ->send(new EmailContactUs($request->validated(), auth()->user()->load('company')));

        return $this->sendResponse([
            'message' => __('Thank you for contacting us.'),
        ]);
    }
}
